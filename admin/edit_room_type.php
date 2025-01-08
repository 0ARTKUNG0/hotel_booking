<?php
session_start();
include '../includes/config.php';
include 'functions.php';
checkAdminAccess();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = $conn->query("SELECT * FROM room_types WHERE id = $id")->fetch_assoc();

if (!$type) {
    header("Location: room_types.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);
    $capacity = $conn->real_escape_string($_POST['capacity']);
    
    $sql = "UPDATE room_types SET name = ?, description = ?, price = ?, capacity = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdii", $name, $description, $price, $capacity, $id);
    
    if ($stmt->execute()) {
        header("Location: room_types.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room Type</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Room Type</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" 
                       value="<?php echo $type['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3" required><?php echo $type['description']; ?></textarea>
            </div>
            <div class="mb-3">
                <label>Price per Night</label>
                <input type="number" name="price" class="form-control" step="0.01" 
                       value="<?php echo $type['price']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Capacity</label>
                <input type="number" name="capacity" class="form-control" 
                       value="<?php echo $type['capacity']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Room Type</button>
            <a href="room_types.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>