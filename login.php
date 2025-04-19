<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();

  if ($user && password_verify($password, $user['password'])) {
    session_start();
    // بعد التحقق من صحة تسجيل الدخول
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_role'] = $user['role']; // تأكد من وجود هذا السطر

// أضف هذا للتحقق
error_log("User logged in: ID={$user['id']}, Role={$user['role']}");
    header("Location: dashboard.php");
    exit();
  } else {
    echo "<script>alert('Invalid email or password'); window.location='login.php';</script>";
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sportify | Login</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    body {
      background-color: #f4f6f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .login-container {
      background-color: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
      text-align: center;
    }

    .login-container h2 {
      color: #007bff;
      margin-bottom: 25px;
    }

    .login-container input {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    .login-container button {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 12px 25px;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
    }

    .login-container button:hover {
      background-color: #0056b3;
    }

    .login-container p {
      margin-top: 20px;
    }

    .login-container a {
      color: #007bff;
      text-decoration: none;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h2>Login to Sportify</h2>
    <form action="login.php" method="POST">
    <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
  </div>

</body>
</html>
