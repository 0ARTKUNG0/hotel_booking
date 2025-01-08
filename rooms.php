<?php
session_start();
include 'includes/config.php';
include 'includes/header.php';
?>
<div class="page-header">
    <div class="container">
        <h1>Our Rooms</h1>
        <p>Find your perfect stay with us</p>
    </div>
</div>

<div class="container mt-4">
    <div class="search-container">
        <form method="GET" class="search-form">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        <input type="date" name="check_in" class="form-control" value="<?php echo $_GET['check_in'] ?? ''; ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        <input type="date" name="check_out" class="form-control" value="<?php echo $_GET['check_out'] ?? ''; ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Check Availability</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="container mt-4">
    <div class="room-grid">
        <?php
$query = "
SELECT r.id, rt.name as type, rt.price, 
       CONCAT('room-', r.id, '.jpg') as image, 
       rt.description
FROM rooms r
JOIN room_types rt ON r.room_type_id = rt.id
WHERE r.status = 'available'
";
$result = $conn->query($query);
$rooms = $result->fetch_all(MYSQLI_ASSOC);

foreach ($rooms as $room):
        ?>
            <div class="room-card fade-in">
                <div class="room-image">
                    <img src="assets/images/<?php echo $room['image']; ?>" alt="<?php echo $room['type']; ?>">
                    <div class="room-price">
                        <span>$<?php echo $room['price']; ?></span>
                        <small>per night</small>
                    </div>
                </div>
                <div class="room-info">
                    <h3><?php echo $room['type']; ?></h3>
                    <p><?php echo $room['description']; ?></p>
                    <div class="room-features">
                        <span><i class="fas fa-wifi"></i> Free WiFi</span>
                        <span><i class="fas fa-tv"></i> Smart TV</span>
                        <span><i class="fas fa-snowflake"></i> AC</span>
                    </div>
                    <a href="book.php?room_id=<?php echo $room['id']; ?>" class="btn btn-primary mt-3">Book Now</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.page-header {
    background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('assets/images/rooms-header.jpg');
    background-size: cover;
    background-position: center;
    color: white;
    padding: 60px 0;
    text-align: center;
    margin-bottom: 40px;
}

.search-container {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    margin-bottom: 40px;
}
.room-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    padding: 20px 0;
}

.room-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.room-card:hover {
    transform: translateY(-5px);
}

.room-image {
    position: relative;
    height: 250px;
}

.room-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.room-price {
    position: absolute;
    right: 20px;
    top: 20px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    text-align: center;
}

.room-info {
    padding: 20px;
}

.room-features {
    display: flex;
    gap: 15px;
    margin: 15px 0;
}

.room-features span {
    font-size: 0.9em;
    color: #666;
}

.room-features i {
    margin-right: 5px;
    color: var(--accent);
}
</style>

<?php include 'includes/footer.php'; ?>