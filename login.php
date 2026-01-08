<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "db_kiosk");

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek User di Database
    $result = mysqli_query($conn, "SELECT * FROM admins WHERE username = '$username'");
    
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Cek Password (Sederhana: Text biasa vs Text biasa)
        // Jika nanti mau pakai hash, ganti jadi: if(password_verify($password, $row['password']))
        if ($password == $row['password']) {
            // Set Session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = $username;
            header("Location: admin.php");
            exit;
        }
    }
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - K5</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #54524A;
            font-family: 'Manrope', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-card {
            background: #FDFCF8;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        }
        h1 { color: #2D2D2A; margin-bottom: 30px; }
        input {
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box; /* Penting agar padding tidak melebarkan input */
        }
        button {
            width: 100%;
            padding: 15px;
            background: #2D2D2A;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover { background: #000; }
        .error { color: red; margin-bottom: 15px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <h1>Admin Login</h1>
        <?php if(isset($error)): ?>
            <p class="error">Username atau Password salah!</p>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Masuk Dashboard</button>
        </form>
    </div>
</body>
</html>