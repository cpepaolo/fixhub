<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'technician') {
    header("Location: index.php");
    exit();
}

// Get technician info
$tech = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT t.*, u.name, u.email, u.phone FROM technicians t
     JOIN users u ON t.user_id = u.id
     WHERE t.user_id = '{$_SESSION['user_id']}'"));

// Update booking status
if (isset($_GET['complete'])) {
    $bid = $_GET['complete'];
    mysqli_query($conn, "UPDATE bookings SET status='completed' WHERE id='$bid' AND technician_id='{$tech['id']}'");
    header("Location: tech_dashboard.php");
    exit();
}

if (isset($_GET['confirm'])) {
    $bid = $_GET['confirm'];
    mysqli_query($conn, "UPDATE bookings SET status='confirmed' WHERE id='$bid' AND technician_id='{$tech['id']}'");
    header("Location: tech_dashboard.php");
    exit();
}

// Get all bookings for this technician
$bookings = mysqli_query($conn,
    "SELECT b.*, u.name as customer_name, u.phone as customer_phone, u.address as customer_address
     FROM bookings b
     JOIN users u ON b.customer_id = u.id
     WHERE b.technician_id = '{$tech['id']}'
     ORDER BY b.created_at DESC");

// Count stats
$total = mysqli_num_rows($bookings);
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings WHERE technician_id='{$tech['id']}' AND status='pending'"))['c'];
$confirmed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings WHERE technician_id='{$tech['id']}' AND status='confirmed'"))['c'];
$completed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings WHERE technician_id='{$tech['id']}' AND status='completed'"))['c'];

// Reset query
$bookings = mysqli_query($conn,
    "SELECT b.*, u.name as customer_name, u.phone as customer_phone, u.address as customer_address
     FROM bookings b
     JOIN users u ON b.customer_id = u.id
     WHERE b.technician_id = '{$tech['id']}'
     ORDER BY b.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FixHub — Technician Dashboard</title>
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

.main { padding: 40px; max-width: 1000px; margin: 0 auto; }
.page-header { margin-bottom: 28px; }
.page-header h1 { font-size: 28px; font-weight: 800; margin-bottom: 6px; }
.page-header p { color: rgba(255,255,255,0.4); font-size: 14px; }

.stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 32px;
}
.stat-card {
    background: #111111;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
}
.stat-num {
    font-size: 32px;
    font-weight: 800;
    color: #FF5A00;
    margin-bottom: 4px;
}
.stat-lbl {
    font-size: 12px;
    color: rgba(255,255,255,0.4);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 16px;
}
.booking-list { display: flex; flex-direction: column; gap: 14px; }
.booking-card {
    background: #111111;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 14px;
    padding: 22px 24px;
    display: flex;
    align-items: center;
    gap: 18px;
    transition: all 0.2s;
}
.booking-card:hover { border-color: rgba(255,255,255,0.1); }
.booking-avatar {
    width: 48px;
    height: 48px;
    background: rgba(255,90,0,0.15);
    border: 2px solid rgba(255,90,0,0.3);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 800;
    color: #FF5A00;
    flex-shrink: 0;
}
.booking-info { flex: 1; }
.booking-info h3 { font-size: 15px; font-weight: 700; margin-bottom: 6px; }
.booking-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 12px;
    color: rgba(255,255,255,0.4);
}
.booking-meta span { display: flex; align-items: center; gap: 5px; }
.booking-meta i { color: #FF5A00; }
.booking-right { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; }
.status {
    padding: 4px 12px;
    border-radius: 100px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.status.pending { background: rgba(245,158,11,0.1); color: #f59e0b; border: 1px solid rgba(245,158,11,0.2); }
.status.confirmed { background: rgba(34,197,94,0.1); color: #22c55e; border: 1px solid rgba(34,197,94,0.2); }
.status.completed { background: rgba(99,102,241,0.1); color: #818cf8; border: 1px solid rgba(99,102,241,0.2); }
.actions { display: flex; gap: 8px; }
.btn-confirm {
    padding: 7px 14px;
    background: rgba(34,197,94,0.1);
    color: #22c55e;
    border: 1px solid rgba(34,197,94,0.3);
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-confirm:hover { background: rgba(34,197,94,0.2); }
.btn-complete {
    padding: 7px 14px;
    background: rgba(255,90,0,0.1);
    color: #FF5A00;
    border: 1px solid rgba(255,90,0,0.3);
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-complete:hover { background: rgba(255,90,0,0.2); }
.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: rgba(255,255,255,0.3);
}
.empty-state i { font-size: 52px; margin-bottom: 16px; display: block; }
.empty-state h3 { font-size: 18px; font-weight: 700; margin-bottom: 8px; color: rgba(255,255,255,0.4); }
</style>
</head>
<body>

<nav class="navbar">
    <div class="logo">
        <div class="logo-icon">🔧</div>
        <div class="logo-text">Fix<span>Hub</span></div>
    </div>
    <div class="nav-right">
        <div class="nav-user">
            🔧 Technician: <span><?php echo $_SESSION['user_name']; ?></span>
        </div>
        <a href="logout.php" class="nav-link logout">Logout</a>
    </div>
</nav>

<div class="main">
    <div class="page-header">
        <h1>My Dashboard</h1>
        <p>Manage your incoming bookings — <?php echo $tech['skill']; ?> · <?php echo $tech['location']; ?></p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-num"><?php echo $total; ?></div>
            <div class="stat-lbl">Total Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:#f59e0b"><?php echo $pending; ?></div>
            <div class="stat-lbl">Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:#22c55e"><?php echo $confirmed; ?></div>
            <div class="stat-lbl">Confirmed</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:#818cf8"><?php echo $completed; ?></div>
            <div class="stat-lbl">Completed</div>
        </div>
    </div>

    <div class="section-title">Incoming Bookings</div>
    <div class="booking-list">
    <?php if(mysqli_num_rows($bookings) > 0): ?>
        <?php while($b = mysqli_fetch_assoc($bookings)): ?>
        <div class="booking-card">
            <div class="booking-avatar"><?php echo strtoupper(substr($b['customer_name'], 0, 1)); ?></div>
            <div class="booking-info">
                <h3><?php echo $b['customer_name']; ?></h3>
                <div class="booking-meta">
                    <span><i class="fas fa-phone"></i><?php echo $b['customer_phone']; ?></span>
                    <span><i class="fas fa-calendar"></i><?php echo date('M d, Y', strtotime($b['service_date'])); ?></span>
                    <span><i class="fas fa-tag"></i><?php echo $b['service_type']; ?></span>
                    <?php if($b['customer_address']): ?>
                    <span><i class="fas fa-map-marker-alt"></i><?php echo $b['customer_address']; ?></span>
                    <?php endif; ?>
                </div>
                <?php if($b['notes']): ?>
                <div style="margin-top:8px; font-size:12px; color:rgba(255,255,255,0.3);">
                    📝 <?php echo $b['notes']; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="booking-right">
                <span class="status <?php echo $b['status']; ?>"><?php echo $b['status']; ?></span>
                <div class="actions">
                    <?php if($b['status'] == 'pending'): ?>
                    <a href="tech_dashboard.php?confirm=<?php echo $b['id']; ?>" class="btn-confirm">Confirm</a>
                    <?php endif; ?>
                    <?php if($b['status'] == 'confirmed'): ?>
                    <a href="tech_dashboard.php?complete=<?php echo $b['id']; ?>" class="btn-complete">Mark Done</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>No bookings yet</h3>
            <p>Your incoming bookings will appear here.</p>
        </div>
    <?php endif; ?>
    </div>
</div>

</body>
</html>