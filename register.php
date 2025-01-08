<?php
session_start();
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $conn->real_escape_string($_POST['email']);
    $full_name = $conn->real_escape_string($_POST['full_name']);
    
    $check_user = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($check_user);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        $error = "Username or email already exists";
    } else {
        $sql = "INSERT INTO users (username, password, email, full_name) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $password, $email, $full_name);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Registration successful. Please login.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Registration failed";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Register - Hotel Booking</title>
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

       .register-box {
           background: rgba(255, 255, 255, 0.2);
           border-radius: 20px;
           padding: 40px;
           width: 400px;
           box-shadow: 0 15px 35px rgba(0,0,0,0.2);
           text-align: center;
           backdrop-filter: blur(10px);
       }

       .register-box h2, .register-box p {
           color: white;
       }

       .form-control {
           background: rgba(255, 255, 255, 0.8);
           border: none;
           border-radius: 10px;
           padding: 15px;
           margin-bottom: 20px;
       }

       .btn-register {
           background: #3498db;
           color: white;
           padding: 15px;
           border-radius: 10px;
           width: 100%;
           font-weight: 500;
           margin-top: 10px;
           transition: all 0.3s;
       }

       .btn-register:hover {
           background: #2980b9;
           transform: translateY(-2px);
       }

       .login-link {
           margin-top: 20px;
           color: white;
       }

       .login-link a {
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
   <div class="register-box">
       <h2>Create Account</h2>
       <p class="mb-4">Please fill in your details</p>

       <?php if(isset($error)): ?>
           <div class="error-message"><?php echo $error; ?></div>
       <?php endif; ?>

       <form method="POST">
           <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
           <input type="text" name="username" class="form-control" placeholder="Username" required>
           <input type="email" name="email" class="form-control" placeholder="Email" required>
           <input type="password" name="password" class="form-control" placeholder="Password" required>
           <button type="submit" class="btn btn-register">Register</button>
       </form>

       <div class="login-link">
           Already have an account? <a href="login.php">Login here</a>
       </div>
   </div>
</body>
</html>