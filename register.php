<?php
session_start();
include 'db.php';
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already exists!";
    } else {
        $sql = "INSERT INTO users (name, email, password, phone, address, role) 
                VALUES ('$name', '$email', '$password', '$phone', '$address', 'customer')";
        if (mysqli_query($conn, $sql)) {
            $success = "Account created successfully! You can now login.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FixHub — Register</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Inter', sans-serif;
    background: #0a0a0a;
    color: #fff;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
}
.container {
    width: 100%;
    max-width: 500px;
}
.logo {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 36px;
    justify-content: center;
}
.logo-icon {
    width: 42px;
    height: 42px;
    background: linear-gradient(135deg, #FF5A00, #ff8c00);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 8px 24px rgba(255,90,0,0.4);
}
.logo-text { font-size: 24px; font-weight: 800; }
.logo-text span { color: #FF5A00; }
.card {
    background: #111111;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 16px;
    padding: 40px;
}
.card-header { margin-bottom: 28px; }
.card-header h2 { font-size: 24px; font-weight: 800; margin-bottom: 6px; }
.card-header p { color: rgba(255,255,255,0.4); font-size: 14px; }
.form-group { margin-bottom: 18px; }
.form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 8px;
}
.input-wrap { position: relative; }
.input-wrap i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,0.25);
    font-size: 14px;
}
.form-group input {
    width: 100%;
    padding: 13px 16px 13px 44px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 10px;
    color: #fff;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    outline: none;
    transition: all 0.2s;
}
.form-group input:focus {
    border-color: #FF5A00;
    background: rgba(255,90,0,0.05);
    box-shadow: 0 0 0 3px rgba(255,90,0,0.1);
}
.form-group input::placeholder { color: rgba(255,255,255,0.2); }
.btn {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #FF5A00, #ff8c00);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 8px;
    box-shadow: 0 8px 24px rgba(255,90,0,0.35);
}
.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 32px rgba(255,90,0,0.45);
}
.error-msg {
    background: rgba(255,68,68,0.1);
    border: 1px solid rgba(255,68,68,0.3);
    color: #ff6b6b;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 20px;
}
.success-msg {
    background: rgba(34,197,94,0.1);
    border: 1px solid rgba(34,197,94,0.3);
    color: #4ade80;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 20px;
}
.bottom-link {
    text-align: center;
    margin-top: 20px;
    font-size: 13px;
    color: rgba(255,255,255,0.4);
}
.bottom-link a { color: #FF5A00; text-decoration: none; font-weight: 600; }
</style>
</head>
<body>
<div class="container">
    <div class="logo">
        <div class="logo-icon">🔧</div>
        <div class="logo-text">Fix<span>Hub</span></div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2>Create Account</h2>
            <p>Register as a customer to book technicians</p>
        </div>

        <?php if($error) echo "<div class='error-msg'><i class='fas fa-exclamation-circle'></i> $error</div>"; ?>
        <?php if($success) echo "<div class='success-msg'><i class='fas fa-check-circle'></i> $success</div>"; ?>

        <?php if(!$success): ?>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <div class="input-wrap">
                    <i class="fas fa-user"></i>
                    <input type="text" name="name" placeholder="Juan dela Cruz" required/>
                </div>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="yourname@email.com" required/>
                </div>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <div class="input-wrap">
                    <i class="fas fa-phone"></i>
                    <input type="text" name="phone" placeholder="09XXXXXXXXX" required/>
                </div>
            </div>
            <div class="form-group">
                <label>Address</label>
                <div class="input-wrap">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="address" placeholder="San Pablo City, Laguna" required/>
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Create a password" required/>
                </div>
            </div>
            <button type="submit" class="btn">Create Account</button>
        </form>
        <?php endif; ?>

        <div class="bottom-link">
            Already have an account? <a href="index.php">Login here</a>
        </div>
    </div>
</div>
</body>
</html>