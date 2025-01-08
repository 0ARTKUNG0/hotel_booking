<?php
session_start();
include '../includes/config.php';
include 'functions.php';
checkAdminAccess();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Panel</a>
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
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Bookings</h5>
                        <?php
                        $result = $conn->query("SELECT COUNT(*) as total FROM bookings");
                        $row = $result->fetch_assoc();
                        ?>
                        <p class="card-text h2"><?php echo $row['total']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Rooms</h5>
                        <?php
                        $result = $conn->query("SELECT COUNT(*) as total FROM rooms");
                        $row = $result->fetch_assoc();
                        ?>
                        <p class="card-text h2"><?php echo $row['total']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <?php
                        $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='user'");
                        $row = $result->fetch_assoc();
                        ?>
                        <p class="card-text h2"><?php echo $row['total']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>