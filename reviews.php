<?php
session_start();
include 'includes/config.php';

$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $rating = $conn->real_escape_string($_POST['rating']);
    $review = $conn->real_escape_string($_POST['review']);
    $user_id = $_SESSION['user_id'];
    
    $sql = "INSERT INTO reviews (user_id, room_id, rating, review) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $user_id, $room_id, $rating, $review);
    $stmt->execute();
}

$reviews = $conn->query("
    SELECT r.*, u.full_name, rm.room_number 
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN rooms rm ON r.room_id = rm.id
    WHERE r.room_id = $room_id
    ORDER BY r.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Room Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="card mb-4">
                <div class="card-header">Write a Review</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Rating</label>
                            <select name="rating" class="form-control" required>
                                <option value="5">⭐⭐⭐⭐⭐</option>
                                <option value="4">⭐⭐⭐⭐</option>
                                <option value="3">⭐⭐⭐</option>
                                <option value="2">⭐⭐</option>
                                <option value="1">⭐</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Review</label>
                            <textarea name="review" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php while ($review = $reviews->fetch_assoc()): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title"><?php echo $review['full_name']; ?></h5>
                                <div>
                                    <?php for($i = 0; $i < $review['rating']; $i++): ?>
                                        <i class="fas fa-star text-warning"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="card-text"><?php echo $review['review']; ?></p>
                            <small class="text-muted">
                                Posted on <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>