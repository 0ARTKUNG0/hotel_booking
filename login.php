<?php
session_start();
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: " . ($user['role'] == 'admin' ? 'admin/index.php' : 'index.php'));
            exit();
        }
    }
    $error = "Invalid username or password";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Hotel Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), 
                        url('assets/images/login-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.2);  /* Changed from 0.9 to 0.2 */
            border-radius: 20px;
            padding: 40px;
            width: 400px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            text-align: center;
            backdrop-filter: blur(10px);
        }

        /* Update text color for better visibility on transparent background */
        .login-box h2, .login-box p {
            color: white;
        }

        .form-control {
            background: #f8f9fa;
            border: none;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }

        .btn-login {
            background: #3498db;
            color: white;
            padding: 15px;
            border-radius: 10px;
            width: 100%;
            font-weight: 500;
            margin-top: 10px;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .register-link {
            margin-top: 20px;
            color: #666;
        }

        .register-link a {
            color: #3498db;
            text-decoration: none;
        }

        .error-message {
            background: #ff000010;
            color: #dc3545;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .back-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 15px;
            border-radius: 10px;
            backdrop-filter: blur(5px);
            transition: all 0.3s;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-btn">
        <i class="fas fa-home"></i>
    </a>
    <div class="login-box">
        <h2>Welcome Back</h2>
        <p class="mb-4">Please login to your account</p>

        <?php if(isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <button type="submit" class="btn btn-login">Login</button>
        </form>

        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html>