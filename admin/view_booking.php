<?php
session_start();
include '../includes/config.php';
include 'functions.php';
checkAdminAccess();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$booking = $conn->query("
    SELECT b.*, u.full_name, u.email, r.room_number, rt.name as room_type, rt.price
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN rooms r ON b.room_id = r.id
    JOIN room_types rt ON r.room_type_id = rt.id
    WHERE b.id = $id
")->fetch_assoc();

if (!$booking) {
    header("Location: bookings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Booking Details</h2>
        
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Booking ID</dt>
                    <dd class="col-sm-9"><?php echo $booking['id']; ?></dd>
                    
                    <dt class="col-sm-3">Guest Name</dt>
                    <dd class="col-sm-9"><?php echo $booking['full_name']; ?></dd>
                    
                    <dt class="col-sm-3">Guest Email</dt>
                    <dd class="col-sm-9"><?php echo $booking['email']; ?></dd>
                    
                    <dt class="col-sm-3">Room Number</dt>
                    <dd class="col-sm-9"><?php echo $booking['room_number']; ?></dd>
                    
                    <dt class="col-sm-3">Room Type</dt>
                    <dd class="col-sm-9"><?php echo $booking['room_type']; ?></dd>
                    
                    <dt class="col-sm-3">Check In Date</dt>
                    <dd class="col-sm-9"><?php echo $booking['check_in']; ?></dd>
                    
                    <dt class="col-sm-3">Check Out Date</dt>
                    <dd class="col-sm-9"><?php echo $booking['check_out']; ?></dd>
                    
                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9"><?php echo ucfirst($booking['status']); ?></dd>
                    
                    <dt class="col-sm-3">Booking Date</dt>
                    <dd class="col-sm-9"><?php echo $booking['created_at']; ?></dd>
                </dl>
            </div>
        </div>
        
        <a href="bookings.php" class="btn btn-secondary mt-3">Back to Bookings</a>
    </div>
</body>
</html>