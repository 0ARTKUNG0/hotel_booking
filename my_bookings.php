<?php
session_start();
include 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "
    SELECT b.*, r.room_number, rt.name as room_type, rt.price
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id 
    JOIN room_types rt ON r.room_type_id = rt.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>My Bookings</h2>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Booking confirmed successfully!</div>
        <?php endif; ?>

        <div class="row">
            <?php while ($booking = $bookings->fetch_assoc()): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Room <?php echo $booking['room_number']; ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo $booking['room_type']; ?></h6>
                            
                            <p class="card-text">
                                Check In: <?php echo $booking['check_in']; ?><br>
                                Check Out: <?php echo $booking['check_out']; ?><br>
                                Status: <span class="badge bg-<?php echo $booking['status'] == 'confirmed' ? 'success' : 
                                    ($booking['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </p>
                            
                            <?php if ($booking['status'] == 'pending'): ?>
                                <form method="POST" action="cancel_booking.php">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Are you sure you want to cancel this booking?')">
                                        Cancel Booking
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>