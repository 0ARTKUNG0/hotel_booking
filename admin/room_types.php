<?php
session_start();
include '../includes/config.php';
include 'functions.php';
checkAdminAccess();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = $conn->real_escape_string($_POST['name']);
                $description = $conn->real_escape_string($_POST['description']);
                $price = $conn->real_escape_string($_POST['price']);
                $capacity = $conn->real_escape_string($_POST['capacity']);
                
                $sql = "INSERT INTO room_types (name, description, price, capacity) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdi", $name, $description, $price, $capacity);
                $stmt->execute();
                break;

            case 'delete':
                $id = $conn->real_escape_string($_POST['type_id']);
                $conn->query("DELETE FROM room_types WHERE id = $id");
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
    <title>Manage Room Types</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Manage Room Types</h2>
        
        <!-- Add Room Type Form -->
        <div class="card mb-4">
            <div class="card-header">Add New Room Type</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label>Price per Night</label>
                                <input type="number" name="price" class="form-control" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label>Capacity</label>
                                <input type="number" name="capacity" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Room Type</button>
                </form>
            </div>
        </div>

        <!-- Room Types List -->
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Capacity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $types = $conn->query("SELECT * FROM room_types");
                        while ($type = $types->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $type['name']; ?></td>
                            <td><?php echo $type['description']; ?></td>
                            <td>$<?php echo $type['price']; ?></td>
                            <td><?php echo $type['capacity']; ?></td>
                            <td>
                                <a href="edit_room_type.php?id=<?php echo $type['id']; ?>" 
                                   class="btn btn-primary btn-sm">Edit</a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="type_id" value="<?php echo $type['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>