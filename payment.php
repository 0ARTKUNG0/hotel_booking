<?php
session_start();
include 'includes/config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['booking_id'])) {
    header("Location: my_bookings.php");
    exit();
}

$booking_id = (int)$_GET['booking_id'];
$booking = $conn->query("
    SELECT b.*, r.room_number, rt.price, rt.name as room_type
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    JOIN room_types rt ON r.room_type_id = rt.id
    WHERE b.id = $booking_id AND b.user_id = {$_SESSION['user_id']}
")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Simulate payment processing
    $payment_status = 'completed';
    
    if ($payment_status == 'completed') {
        $conn->query("UPDATE bookings SET status = 'confirmed' WHERE id = $booking_id");
        header("Location: my_bookings.php?payment=success");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Payment Details</div>
                    <div class="card-body">
                        <p>Room: <?php echo $booking['room_number']; ?> (<?php echo $booking['room_type']; ?>)</p>
                        <p>Check-in: <?php echo $booking['check_in']; ?></p>
                        <p>Check-out: <?php echo $booking['check_out']; ?></p>
                        <hr>
                        <form method="POST">
                            <div class="mb-3">
                                <label>Card Number (Demo)</label>
                                <input type="text" class="form-control" value="4242 4242 4242 4242" readonly>
                            </div>
                            <div class="mb-3">
                                <label>Expiry (Demo)</label>
                                <input type="text" class="form-control" value="12/25" readonly>
                            </div>
                            <div class="mb-3">
                                <label>CVV (Demo)</label>
                                <input type="text" class="form-control" value="123" readonly>
                            </div>
                            <button type="submit" class="btn btn-primary">Complete Payment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>