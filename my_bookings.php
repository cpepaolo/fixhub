<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: index.php");
    exit();
}

// Cancel booking
if (isset($_GET['cancel'])) {
    $cancel_id = $_GET['cancel'];
    mysqli_query($conn, "DELETE FROM bookings WHERE id='$cancel_id' AND customer_id='{$_SESSION['user_id']}'");
    header("Location: my_bookings.php");
    exit();
}

$bookings = mysqli_query($conn, 
    "SELECT b.*, u.name as tech_name, t.skill, t.location 
     FROM bookings b 
     JOIN technicians t ON b.technician_id = t.id 
     JOIN users u ON t.user_id = u.id 
     WHERE b.customer_id = '{$_SESSION['user_id']}' 
     ORDER BY b.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FixHub — My Bookings</title>
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
.logo { display: flex; align-items: center; gap: 10px; }
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
.nav-link.active { background: rgba(255,90,0,0.15); color: #FF5A00; }
.nav-link.logout:hover { color: #ff6b6b; background: rgba(255,68,68,0.08); }

.main { padding: 40px; max-width: 900px; margin: 0 auto; }
.page-header { margin-bottom: 32px; }
.page-header h1 { font-size: 28px; font-weight: 800; margin-bottom: 6px; }
.page-header p { color: rgba(255,255,255,0.4); font-size: 14px; }

.booking-list { display: flex; flex-direction: column; gap: 16px; }
.booking-card {
    background: #111111;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 14px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.2s;
}
.booking-card:hover { border-color: rgba(255,255,255,0.1); }
.booking-avatar {
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
.booking-info { flex: 1; }
.booking-info h3 { font-size: 16px; font-weight: 700; margin-bottom: 6px; }
.booking-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 13px;
    color: rgba(255,255,255,0.4);
}
.booking-meta span { display: flex; align-items: center; gap: 6px; }
.booking-meta i { color: #FF5A00; }
.booking-right { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; }
.status {
    padding: 5px 14px;
    border-radius: 100px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.status.pending { background: rgba(245,158,11,0.1); color: #f59e0b; border: 1px solid rgba(245,158,11,0.2); }
.status.confirmed { background: rgba(34,197,94,0.1); color: #22c55e; border: 1px solid rgba(34,197,94,0.2); }
.status.completed { background: rgba(99,102,241,0.1); color: #818cf8; border: 1px solid rgba(99,102,241,0.2); }
.status.cancelled { background: rgba(255,68,68,0.1); color: #ff6b6b; border: 1px solid rgba(255,68,68,0.2); }
.btn-cancel {
    padding: 7px 16px;
    background: transparent;
    color: #ff6b6b;
    border: 1px solid rgba(255,68,68,0.3);
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}
.btn-cancel:hover { background: rgba(255,68,68,0.1); }
.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: rgba(255,255,255,0.3);
}
.empty-state i { font-size: 52px; margin-bottom: 16px; display: block; }
.empty-state h3 { font-size: 18px; font-weight: 700; margin-bottom: 8px; color: rgba(255,255,255,0.4); }
.empty-state p { font-size: 14px; margin-bottom: 24px; }
.btn-find {
    display: inline-block;
    padding: 12px 28px;
    background: linear-gradient(135deg, #FF5A00, #ff8c00);
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 700;
    font-size: 14px;
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
            <a href="my_bookings.php" class="nav-link active">My Bookings</a>
            <a href="logout.php" class="nav-link logout">Logout</a>
        </div>
    </div>
</nav>

<div class="main">
    <div class="page-header">
        <h1>My Bookings</h1>
        <p>Track and manage all your service bookings</p>
    </div>

    <div class="booking-list">
    <?php if(mysqli_num_rows($bookings) > 0): ?>
        <?php while($b = mysqli_fetch_assoc($bookings)): ?>
        <div class="booking-card">
            <div class="booking-avatar"><?php echo strtoupper(substr($b['tech_name'], 0, 1)); ?></div>
            <div class="booking-info">
                <h3><?php echo $b['tech_name']; ?></h3>
                <div class="booking-meta">
                    <span><i class="fas fa-wrench"></i><?php echo $b['skill']; ?></span>
                    <span><i class="fas fa-calendar"></i><?php echo date('M d, Y', strtotime($b['service_date'])); ?></span>
                    <span><i class="fas fa-tag"></i><?php echo $b['service_type']; ?></span>
                    <span><i class="fas fa-map-marker-alt"></i><?php echo $b['location']; ?></span>
                </div>
                <?php if($b['notes']): ?>
                <div style="margin-top:8px; font-size:13px; color:rgba(255,255,255,0.3);">
                    📝 <?php echo $b['notes']; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="booking-right">
                <span class="status <?php echo $b['status']; ?>"><?php echo $b['status']; ?></span>
                <?php if($b['status'] == 'pending'): ?>
                <a href="my_bookings.php?cancel=<?php echo $b['id']; ?>" 
                   class="btn-cancel"
                   onclick="return confirm('Are you sure you want to cancel this booking?')">
                   Cancel
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>No bookings yet</h3>
            <p>You haven't made any bookings yet.</p>
            <a href="technicians.php" class="btn-find">Find a Technician</a>
        </div>
    <?php endif; ?>
    </div>
</div>

</body>
</html>