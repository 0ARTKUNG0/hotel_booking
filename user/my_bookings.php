<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "
    SELECT b.*, r.room_number, rt.name as room_type, rt.price,
           b.check_in, b.check_out, b.total_price
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .page-header {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('../assets/images/header-bg.jpg');
            background-size: cover;
            background-position: center;
            padding: 60px 0;
            color: white;
            margin-bottom: 40px;
        }

        .booking-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            transition: transform 0.3s;
        }

        .booking-card:hover {
            transform: translateY(-5px);
        }

        .room-image {
            height: 300px;
            overflow: hidden;
        }

        .room-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .booking-card:hover .room-image img {
            transform: scale(1.05);
        }

        .booking-details {
            padding: 25px;
        }

        .room-type {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .booking-info {
            margin: 20px 0;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .info-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-item i {
            width: 25px;
            color: #3498db;
            margin-right: 15px;
        }

        .booking-actions {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .btn-cancel {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-cancel:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        .no-bookings {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .no-bookings i {
            font-size: 3rem;
            color: #3498db;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="page-header">
        <div class="container">
            <h1>My Bookings</h1>
            <p>View and manage your hotel reservations</p>
        </div>
    </div>

    <div class="container">
        <?php if ($bookings->num_rows > 0): ?>
            <?php while ($booking = $bookings->fetch_assoc()): ?>
                <div class="booking-card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <div class="room-image">
                                <img src="../assets/images/room-<?php echo $booking['room_id']; ?>.jpg" 
                                     alt="Room <?php echo $booking['room_number']; ?>">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="booking-details">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h4>Room <?php echo $booking['room_number']; ?></h4>
                                        <p class="room-type"><?php echo $booking['room_type']; ?></p>
                                    </div>
                                    <div class="status-badge <?php echo strtolower($booking['status']); ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </div>
                                </div>
                                
                                <div class="booking-info">
                                    <div class="info-item">
                                        <i class="fas fa-calendar-check"></i>
                                        <span>Check In: <?php echo date('M d, Y', strtotime($booking['check_in'])); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-calendar-times"></i>
                                        <span>Check Out: <?php echo date('M d, Y', strtotime($booking['check_out'])); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span>Total Price: $<?php echo $booking['total_price'] ? number_format($booking['total_price'], 2) : '0.00'; ?></span>
                                    </div>
                                </div>

                                <?php if ($booking['status'] == 'pending'): ?>
                                    <div class="booking-actions">
                                        <form method="POST" action="../cancel_booking.php">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" class="btn btn-cancel" 
                                                    onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                <i class="fas fa-times-circle"></i> Cancel Booking
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-bookings">
                <i class="fas fa-calendar-times"></i>
                <h3>No Bookings Found</h3>
                <p>You haven't made any bookings yet.</p>
                <a href="../rooms.php" class="btn btn-primary mt-3">Browse Rooms</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>