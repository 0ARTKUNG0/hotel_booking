<?php
session_start();
include '../includes/config.php';
include 'functions.php';
checkAdminAccess();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $room_number = $conn->real_escape_string($_POST['room_number']);
                $room_type = $conn->real_escape_string($_POST['room_type']);
                
                $sql = "INSERT INTO rooms (room_number, room_type_id, status) VALUES (?, ?, 'available')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $room_number, $room_type);
                
                if ($stmt->execute()) {
                    $new_room_id = $conn->insert_id;
                    
                    // Handle image upload
                    if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
                        $allowed = ['jpg', 'jpeg', 'png'];
                        $filename = $_FILES['room_image']['name'];
                        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        
                        if (in_array($ext, $allowed)) {
                            $new_filename = 'room-' . $new_room_id . '.' . $ext;
                            $upload_path = '../assets/images/' . $new_filename;
                            move_uploaded_file($_FILES['room_image']['tmp_name'], $upload_path);
                        }
                    }
                }
                break;

            case 'delete':
                $id = $conn->real_escape_string($_POST['room_id']);
                // Delete room image if exists
                $image_path = '../assets/images/room-' . $id . '.jpg';
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
                $conn->query("DELETE FROM rooms WHERE id = $id");
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php">Admin Panel</a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="rooms.php">Manage Rooms</a></li>
                        <li class="nav-item"><a class="nav-link" href="bookings.php">Manage Bookings</a></li>
                        <li class="nav-item"><a class="nav-link" href="users.php">Manage Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

    <div class="container mt-4">
        <h2>Manage Rooms</h2>
        
        <!-- Add Room Form -->
                    <div class="card mb-4">
                        <div class="card-header">Add New Room</div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="add">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label>Room Number</label>
                                            <input type="text" name="room_number" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label>Room Type</label>
                                            <select name="room_type" class="form-control" required>
                                                <?php
                                                $types = $conn->query("SELECT * FROM room_types");
                                                while ($type = $types->fetch_assoc()) {
                                                    echo "<option value='{$type['id']}'>{$type['name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label>Room Image</label>
                                            <input type="file" name="room_image" class="form-control" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary d-block w-100">Add Room</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

        <!-- Room List -->
        <div class="card">
            <div class="card-header">Room List</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Room Number</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rooms = $conn->query("
                            SELECT r.*, rt.name as room_type 
                            FROM rooms r 
                            JOIN room_types rt ON r.room_type_id = rt.id
                        ");
                        
                        while ($room = $rooms->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $room['room_number']; ?></td>
                            <td><?php echo $room['room_type']; ?></td>
                            <td><?php echo $room['status']; ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                                <a href="edit_room.php?id=<?php echo $room['id']; ?>" 
                                   class="btn btn-primary btn-sm">Edit</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>