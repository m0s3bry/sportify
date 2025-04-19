<?php
include 'includes/db.php'; // الاتصال بقاعدة البيانات

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // تشفير الباسورد

  // التحقق إذا كان البريد الإلكتروني موجود مسبقًا
  $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $check->bind_param("s", $email);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows > 0) {
    echo "<script>alert('Email already exists');</script>";
  } else {
    // إضافة المستخدم في قاعدة البيانات
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
      echo "<script>alert('Registration successful! You can now login.'); window.location='login.php';</script>";
    } else {
      echo "<script>alert('Something went wrong, try again.');</script>";
    }
  }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sportify | Register</title>
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

    .register-container {
      background-color: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
      text-align: center;
    }

    .register-container h2 {
      color: #007bff;
      margin-bottom: 25px;
    }

    .register-container input {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    .register-container button {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 12px 25px;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
    }

    .register-container button:hover {
      background-color: #0056b3;
    }

    .register-container p {
      margin-top: 20px;
    }

    .register-container a {
      color: #007bff;
      text-decoration: none;
    }
  </style>
</head>
<body>

  <div class="register-container">
    <h2>Create an Account</h2>
    <form action="register.php" method="POST">
  <input type="text" name="name" placeholder="Full Name" required />
  <input type="email" name="email" placeholder="Email" required />
  <input type="password" name="password" placeholder="Password" required />
  <button type="submit">Register</button>
</form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
  </div>

</body>
</html>
