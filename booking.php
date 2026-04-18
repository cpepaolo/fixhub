<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: index.php");
    exit();
}

$tech_id = $_GET['tech_id'];
$tech = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT t.*, u.name, u.phone FROM technicians t 
     JOIN users u ON t.user_id = u.id 
     WHERE t.id = '$tech_id'"));

if (!$tech) {
    header("Location: technicians.php");
    exit();
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_SESSION['user_id'];
    $service_date = $_POST['service_date'];
    $service_type = mysqli_real_escape_string($conn, $_POST['service_type']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    $sql = "INSERT INTO bookings (customer_id, technician_id, service_date, service_type, notes, status) 
            VALUES ('$customer_id', '$tech_id', '$service_date', '$service_type', '$notes', 'pending')";

    if (mysqli_query($conn, $sql)) {
        $success = "Booking confirmed! The technician will contact you soon.";
    } else {
        $error = "Something went wrong. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FixHub — Book Technician</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Inter', sans-serif;
    background: #0a0a0a;
    color: #fff;
    min-height: 100vh;
}
.navbar {
    background: #111111;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    padding: 0 40px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 100;
}
.logo {
    display: flex;
    align-items: center;
    gap: 10px;
}
.logo-icon {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #FF5A00, #ff8c00);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}
.logo-text { font-size: 20px; font-weight: 800; }
.logo-text span { color: #FF5A00; }
.nav-right { display: flex; align-items: center; gap: 16px; }
.nav-user { font-size: 13px; color: rgba(255,255,255,0.5); }
.nav-user span { color: #fff; font-weight: 600; }
.nav-links { display: flex; gap: 8px; }
.nav-link {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    color: rgba(255,255,255,0.6);
}
.nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; }
.nav-link.logout:hover { color: #ff6b6b; background: rgba(255,68,68,0.08); }

.main {
    padding: 40px;
    max-width: 800px;
    margin: 0 auto;
}
.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: rgba(255,255,255,0.4);
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 28px;
    transition: color 0.2s;
}
.back-link:hover { color: #fff; }
.page-header { margin-bottom: 28px; }
.page-header h1 { font-size: 28px; font-weight: 800; margin-bottom: 6px; }
.page-header p { color: rgba(255,255,255,0.4); font-size: 14px; }

.tech-summary {
    background: #111111;
    border: 1px solid rgba(255,90,0,0.2);
    border-radius: 14px;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
}
.tech-avatar {
    width: 52px;
    height: 52px;
    background: rgba(255,90,0,0.15);
    border: 2px solid rgba(255,90,0,0.3);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 800;
    color: #FF5A00;
    flex-shrink: 0;
}
.tech-summary-info h3 { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
.tech-summary-info p { font-size: 13px; color: rgba(255,255,255,0.4); }
.tech-skill {
    margin-left: auto;
    background: rgba(255,90,0,0.1);
    border: 1px solid rgba(255,90,0,0.2);
    color: #FF5A00;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 100px;
}

.card {
    background: #111111;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 14px;
    padding: 32px;
}
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
.input-wrap { position: relative; }
.input-wrap i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,0.25);
    font-size: 14px;
}
.form-group input,
.form-group select,
.form-group textarea {
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
.form-group select { padding: 13px 16px; }
.form-group textarea { padding: 13px 16px; resize: none; height: 100px; }
.form-group select option { background: #1a1a1a; }
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #FF5A00;
    background: rgba(255,90,0,0.05);
    box-shadow: 0 0 0 3px rgba(255,90,0,0.1);
}
.form-group input::placeholder,
.form-group textarea::placeholder { color: rgba(255,255,255,0.2); }
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
.btn:hover { opacity: 0.9; transform: translateY(-1px); }
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
    padding: 16px 20px;
    border-radius: 10px;
    font-size: 14px;
    margin-bottom: 20px;
    text-align: center;
}
.success-msg i { font-size: 24px; display: block; margin-bottom: 8px; }
.success-msg a {
    display: inline-block;
    margin-top: 12px;
    padding: 10px 24px;
    background: #FF5A00;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 700;
    font-size: 13px;
}
</style>
</head>
<body>

<nav class="navbar">
    <div class="logo">
        <div class="logo-icon">🔧</div>
        <div class="logo-text">Fix<span>Hub</span></div>
    </div>
    <div class="nav-right">
        <div class="nav-user">Welcome, <span><?php echo $_SESSION['user_name']; ?></span></div>
        <div class="nav-links">
            <a href="technicians.php" class="nav-link">Find Technicians</a>
            <a href="my_bookings.php" class="nav-link">My Bookings</a>
            <a href="logout.php" class="nav-link logout">Logout</a>
        </div>
    </div>
</nav>

<div class="main">
    <a href="technicians.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Technicians</a>

    <div class="page-header">
        <h1>Book a Technician</h1>
        <p>Fill in the details below to confirm your booking</p>
    </div>

    <div class="tech-summary">
        <div class="tech-avatar"><?php echo strtoupper(substr($tech['name'], 0, 1)); ?></div>
        <div class="tech-summary-info">
            <h3><?php echo $tech['name']; ?></h3>
            <p><i class="fas fa-map-marker-alt"></i> <?php echo $tech['location']; ?></p>
        </div>
        <span class="tech-skill"><?php echo $tech['skill']; ?></span>
    </div>

    <?php if($success): ?>
    <div class="success-msg">
        <i class="fas fa-check-circle"></i>
        <?php echo $success; ?>
        <br>
        <a href="my_bookings.php">View My Bookings</a>
    </div>
    <?php else: ?>

    <?php if($error) echo "<div class='error-msg'>$error</div>"; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label>Service Type</label>
                <select name="service_type" required>
                    <option value="" disabled selected>Select service type</option>
                    <option value="Home Service">Home Service</option>
                    <option value="Drop-off Repair">Drop-off Repair</option>
                    <option value="Emergency Repair">Emergency Repair</option>
                </select>
            </div>
            <div class="form-group">
                <label>Preferred Date</label>
                <div class="input-wrap">
                    <i class="fas fa-calendar"></i>
                    <input type="date" name="service_date" required min="<?php echo date('Y-m-d'); ?>"/>
                </div>
            </div>
            <div class="form-group">
                <label>Additional Notes</label>
                <textarea name="notes" placeholder="Describe the problem or any special instructions..."></textarea>
            </div>
            <button type="submit" class="btn"><i class="fas fa-check"></i> Confirm Booking</button>
        </form>
    </div>
    <?php endif; ?>
</div>

</body>
</html>