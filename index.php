<?php
session_start();
include 'db.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] == 'technician') {
            header("Location: tech_dashboard.php");
        } else {
            header("Location: technicians.php");
        }
        exit();
    } else {
        $error = "Wrong email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FixHub — Login</title>
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
}
.left-panel {
    flex: 1;
    background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 60px;
    position: relative;
    overflow: hidden;
}
.left-panel::before {
    content: '';
    position: absolute;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(255,90,0,0.15) 0%, transparent 70%);
    top: -100px;
    left: -100px;
    border-radius: 50%;
}
.left-panel::after {
    content: '';
    position: absolute;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255,90,0,0.08) 0%, transparent 70%);
    bottom: -100px;
    right: -100px;
    border-radius: 50%;
}
.logo {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 60px;
    position: relative;
    z-index: 1;
}
.logo-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #FF5A00, #ff8c00);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    box-shadow: 0 8px 24px rgba(255,90,0,0.4);
}
.logo-text {
    font-size: 28px;
    font-weight: 800;
    letter-spacing: -0.5px;
}
.logo-text span { color: #FF5A00; }
.hero-title {
    font-size: 52px;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 20px;
    position: relative;
    z-index: 1;
}
.hero-title span { color: #FF5A00; }
.hero-sub {
    font-size: 16px;
    color: rgba(255,255,255,0.5);
    margin-bottom: 50px;
    line-height: 1.6;
    position: relative;
    z-index: 1;
}
.features {
    display: flex;
    flex-direction: column;
    gap: 16px;
    position: relative;
    z-index: 1;
}
.feature {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 20px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 12px;
    backdrop-filter: blur(10px);
}
.feature-icon {
    width: 40px;
    height: 40px;
    background: rgba(255,90,0,0.15);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #FF5A00;
    font-size: 16px;
    flex-shrink: 0;
}
.feature-text h4 { font-size: 14px; font-weight: 600; margin-bottom: 2px; }
.feature-text p { font-size: 12px; color: rgba(255,255,255,0.4); }

.right-panel {
    width: 480px;
    background: #111111;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 60px 50px;
    border-left: 1px solid rgba(255,255,255,0.06);
}
.form-header { margin-bottom: 36px; }
.form-header h2 { font-size: 30px; font-weight: 800; margin-bottom: 8px; }
.form-header p { color: rgba(255,255,255,0.4); font-size: 14px; }
.form-group { margin-bottom: 20px; }
.form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 8px;
}
.input-wrap {
    position: relative;
}
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
    padding: 14px 16px 14px 44px;
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
.btn-login {
    width: 100%;
    padding: 15px;
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
.btn-login:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 32px rgba(255,90,0,0.45);
}
.divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 24px 0;
    color: rgba(255,255,255,0.2);
    font-size: 12px;
}
.divider::before, .divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255,255,255,0.08);
}
.btn-register {
    width: 100%;
    padding: 15px;
    background: transparent;
    color: #fff;
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
    text-decoration: none;
    display: block;
}
.btn-register:hover {
    background: rgba(255,255,255,0.05);
    border-color: rgba(255,255,255,0.2);
}
.error-msg {
    background: rgba(255,68,68,0.1);
    border: 1px solid rgba(255,68,68,0.3);
    color: #ff6b6b;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.tech-login-link {
    text-align: center;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid rgba(255,255,255,0.06);
}
.tech-login-link p {
    font-size: 13px;
    color: rgba(255,255,255,0.4);
    margin-bottom: 8px;
}
.tech-login-link a {
    color: #FF5A00;
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
}
.tech-login-link a:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="left-panel">
    <div class="logo">
        <div class="logo-icon">🔧</div>
        <div class="logo-text">Fix<span>Hub</span></div>
    </div>
    <h1 class="hero-title">Fix anything,<br><span>anytime.</span></h1>
    <p class="hero-sub">Connect with trusted repair technicians<br>in San Pablo City, Laguna.</p>
    <div class="features">
        <div class="feature">
            <div class="feature-icon"><i class="fas fa-bolt"></i></div>
            <div class="feature-text">
                <h4>Fast Booking</h4>
                <p>Book a technician in under 2 minutes</p>
            </div>
        </div>
        <div class="feature">
            <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="feature-text">
                <h4>Verified Experts</h4>
                <p>All techs are ID-verified and rated</p>
            </div>
        </div>
        <div class="feature">
            <div class="feature-icon"><i class="fas fa-map-marker-alt"></i></div>
            <div class="feature-text">
                <h4>Local Technicians</h4>
                <p>Serving San Pablo City and nearby areas</p>
            </div>
        </div>
    </div>
</div>

<div class="right-panel">
    <div class="form-header">
        <h2>Welcome back 👋</h2>
        <p>Log in to your FixHub account</p>
    </div>

    <?php if($error): ?>
    <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Email Address</label>
            <div class="input-wrap">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="yourname@email.com" required/>
            </div>
        </div>
        <div class="form-group">
            <label>Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Enter your password" required/>
            </div>
        </div>
        <button type="submit" class="btn-login">Log In</button>
    </form>

    <div class="divider">or</div>
    <a href="register.php" class="btn-register">Create a Customer Account</a>

    <div class="tech-login-link">
        <p>Are you a technician?</p>
        <a href="tech_register.php">Register as Technician →</a>
    </div>
</div>

</body>
</html>