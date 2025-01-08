<?php
session_start();
include '../includes/config.php';
include 'functions.php';
checkAdminAccess();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $conn->real_escape_string($_POST['booking_id']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $sql = "UPDATE bookings SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $booking_id);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
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
        <h2>Manage Bookings</h2>
        
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Guest Name</th>
                            <th>Room</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $bookings = $conn->query("
                            SELECT b.*, u.full_name, r.room_number 
                            FROM bookings b
                            JOIN users u ON b.user_id = u.id
                            JOIN rooms r ON b.room_id = r.id
                            ORDER BY b.created_at DESC
                        ");
                        
                        while ($booking = $bookings->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $booking['id']; ?></td>
                            <td><?php echo $booking['full_name']; ?></td>
                            <td><?php echo $booking['room_number']; ?></td>
                            <td><?php echo $booking['check_in']; ?></td>
                            <td><?php echo $booking['check_out']; ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $booking['status']=='pending'?'selected':''; ?>>
                                            Pending
                                        </option>
                                        <option value="confirmed" <?php echo $booking['status']=='confirmed'?'selected':''; ?>>
                                            Confirmed
                                        </option>
                                        <option value="cancelled" <?php echo $booking['status']=='cancelled'?'selected':''; ?>>
                                            Cancelled
                                        </option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <a href="view_booking.php?id=<?php echo $booking['id']; ?>" 
                                   class="btn btn-info btn-sm">View Details</a>
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