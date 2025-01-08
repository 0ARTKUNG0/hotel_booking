<?php
session_start();
include 'includes/config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['booking_id'])) {
    header("Location: my_bookings.php");
    exit();
}

$booking_id = (int)$_POST['booking_id'];
$user_id = $_SESSION['user_id'];

$sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();

header("Location: my_bookings.php");