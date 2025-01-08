<?php
session_start();
include 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';

// Get room details
$room = $conn->query("
    SELECT r.*, rt.name as room_type, rt.price, rt.description
    FROM rooms r
    JOIN room_types rt ON r.room_type_id = rt.id
    WHERE r.id = $room_id
")->fetch_assoc();

// Calculate total price
$check_in_date = new DateTime($check_in);
$check_out_date = new DateTime($check_out);
$days = $check_out_date->diff($check_in_date)->days;
$total_price = $days * $room['price'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $check_in = date('Y-m-d', strtotime($_POST['check_in']));
    $check_out = date('Y-m-d', strtotime($_POST['check_out']));
    $total_price = floatval($_POST['total_price']);

    $sql = "INSERT INTO bookings (user_id, room_id, check_in, check_out, total_price, status) 
            VALUES (?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissd", $_SESSION['user_id'], $room_id, $check_in, $check_out, $total_price);
    
    if ($stmt->execute()) {
        header("Location: user/my_bookings.php?success=1");
        exit();
    } else {
        $error = "Booking failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .booking-container {
            max-width: 800px;
            margin: 40px auto;
        }

        .booking-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .booking-header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .room-image {
            width: 100%;
            height: 300px;
            overflow: hidden;
            position: relative;
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

        .booking-body {
            padding: 30px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .detail-label {
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            color: #333;
            font-weight: 600;
        }

        .total-price {
            font-size: 1.5rem;
            color: #2c3e50;
            text-align: right;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-top: 20px;
        }

        .btn-confirm {
            background: #3498db;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            border: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-confirm:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background: #e74c3c;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            border: none;
        }

        .btn-cancel:hover {
            background: #c0392b;
            color: white;
        }

        .form-control {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
</style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="booking-container">
    <div class="booking-card">
        <div class="booking-header">
            <h3>Confirm Booking</h3>
        </div>

        <div class="room-image">
            <img src="assets/images/room-<?php echo $room_id; ?>.jpg" alt="<?php echo $room['room_type']; ?>">
        </div>
        
                <div class="booking-body">
                    <form method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Check In</label>
                                <input type="date" name="check_in" class="form-control" required 
                                    min="<?php echo date('Y-m-d'); ?>" 
                                    value="<?php echo date('Y-m-d'); ?>" 
                                    onchange="calculateDuration()">
                            </div>
                            <div class="col-md-6">
                                <label>Check Out</label>
                                <input type="date" name="check_out" class="form-control" required 
                                    min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" 
                                    value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" 
                                    onchange="calculateDuration()">
                            </div>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Room</span>
                            <span class="detail-value"><?php echo $room['room_number']; ?> (<?php echo $room['room_type']; ?>)</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Duration</span>
                            <span class="detail-value" id="duration">1 night</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Price per night</span>
                            <span class="detail-value">$<?php echo number_format($room['price'], 2); ?></span>
                        </div>

                        <div class="total-price">
                            Total Price: $<span id="total_price">0.00</span>
                        </div>

                        <input type="hidden" name="total_price" id="total_price_input">

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-confirm me-2">Confirm Booking</button>
                            <a href="rooms.php" class="btn btn-cancel">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function calculateDuration() {
                const checkIn = new Date(document.querySelector('input[name="check_in"]').value);
                const checkOut = new Date(document.querySelector('input[name="check_out"]').value);
                
                if (checkIn && checkOut && checkOut > checkIn) {
                    const duration = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                    const pricePerNight = <?php echo $room['price']; ?>;
                    const totalPrice = duration * pricePerNight;
                    
                    document.getElementById('duration').textContent = duration + ' nights';
                    document.getElementById('total_price').textContent = totalPrice.toFixed(2);
                    document.getElementById('total_price_input').value = totalPrice;
                }
            }

            // Calculate initial duration and price
            window.onload = calculateDuration;
        </script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>