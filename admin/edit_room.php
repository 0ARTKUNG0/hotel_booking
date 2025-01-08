<?php
session_start();
include '../includes/config.php';
include 'functions.php';
checkAdminAccess();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$room = $conn->query("
    SELECT r.*, rt.name as room_type 
    FROM rooms r
    JOIN room_types rt ON r.room_type_id = rt.id 
    WHERE r.id = $id
")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_number = $conn->real_escape_string($_POST['room_number']);
    $room_type = $conn->real_escape_string($_POST['room_type']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Handle image upload
    if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['room_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'room-' . $id . '.' . $ext;
            $upload_path = '../assets/images/' . $new_filename;
            move_uploaded_file($_FILES['room_image']['tmp_name'], $upload_path);
        }
    }

    // Update room details
    $sql = "UPDATE rooms SET room_number = ?, room_type_id = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisi", $room_number, $room_type, $status, $id);
    
    if ($stmt->execute()) {
        header("Location: rooms.php?updated=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .page-header {
            background: #2c3e50;
            color: white;
            padding: 20px 0;
            margin-bottom: 40px;
        }
        .edit-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .room-image-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .room-image {
            width: 100%;
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            margin-bottom: 20px;
        }
        .room-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .room-image:hover img {
            transform: scale(1.05);
        }
        .image-upload {
            text-align: center;
        }
        .image-upload label {
            display: block;
            margin-bottom: 10px;
            color: #666;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .form-control {
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 8px;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52,152,219,0.2);
        }
        .btn-group {
            margin-top: 30px;
        }
        .btn {
            padding: 12px 24px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #3498db;
            border: none;
        }
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: #95a5a6;
            border: none;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="page-header">
        <div class="container">
            <h2 class="m-0">Edit Room <?php echo $room['room_number']; ?></h2>
        </div>
    </div>

    <div class="container edit-container">
        <form method="POST" enctype="multipart/form-data">
            <div class="room-image-container">
                <div class="room-image">
                    <img src="../assets/images/room-<?php echo $id; ?>.jpg?v=<?php echo time(); ?>" 
                         alt="Room <?php echo $room['room_number']; ?>"
                         onerror="this.src='../assets/images/default-room.jpg'"
                         id="room-preview">
                </div>
                <div class="image-upload">
                    <label for="room_image">Choose Room Image</label>
                    <input type="file" id="room_image" name="room_image" class="form-control" 
                           accept="image/*" onchange="previewImage(this);">
                </div>
            </div>

            <div class="form-container">
                <div class="mb-4">
                    <label class="form-label" for="room_number">Room Number</label>
                    <input type="text" id="room_number" name="room_number" class="form-control" 
                           value="<?php echo $room['room_number']; ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="room_type">Room Type</label>
                    <select id="room_type" name="room_type" class="form-control" required>
                        <?php
                        $types = $conn->query("SELECT * FROM room_types");
                        while ($type = $types->fetch_assoc()) {
                            $selected = ($type['id'] == $room['room_type_id']) ? 'selected' : '';
                            echo "<option value='{$type['id']}' $selected>{$type['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="available" <?php echo $room['status']=='available'?'selected':''; ?>>
                            Available
                        </option>
                        <option value="booked" <?php echo $room['status']=='booked'?'selected':''; ?>>
                            Booked
                        </option>
                        <option value="maintenance" <?php echo $room['status']=='maintenance'?'selected':''; ?>>
                            Maintenance
                        </option>
                    </select>
                </div>

                <div class="btn-group d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Room
                    </button>
                    <a href="rooms.php" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('room-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>