<?php
session_start();
include 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('assets/images/hotel-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .feature-box {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
            margin: 20px 0;
        }

        .feature-box:hover {
            transform: translateY(-10px);
        }

        .feature-box i {
            font-size: 3rem;
            color: #3498db;
            margin-bottom: 20px;
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 20px;
            animation: fadeInDown 1s;
        }

        .hero p {
            font-size: 1.5rem;
            margin-bottom: 30px;
            animation: fadeInUp 1s;
        }

        .btn-book {
            padding: 15px 40px;
            font-size: 1.2rem;
            border-radius: 30px;
            background: #3498db;
            color: white;
            border: 16px solid white;
            font-weight: bold;
            text-shadow: 1px 1px 3px rgb(255, 255, 255);
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: all 0.3s;
        }

        .btn-book:hover {
            background: #2980b9;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.4);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="hero">
        <div class="container">
            <h1>Welcome to Luxury Hotel</h1>
            <p>Experience comfort and elegance at its finest</p>
            <a href="rooms.php" class="btn btn-book btn-lg">Book Now</a>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fas fa-bed"></i>
                    <h3>Luxurious Rooms</h3>
                    <p>Experience comfort in our well-appointed rooms</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fas fa-concierge-bell"></i>
                    <h3>24/7 Service</h3>
                    <p>Round-the-clock service at your fingertips</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fas fa-wifi"></i>
                    <h3>Free WiFi</h3>
                    <p>Stay connected with high-speed internet</p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>