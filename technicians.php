<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: index.php");
    exit();
}

$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $techs = mysqli_query($conn, "SELECT t.*, u.name, u.phone FROM technicians t 
                                  JOIN users u ON t.user_id = u.id 
                                  WHERE u.name LIKE '%$search%' 
                                  OR t.skill LIKE '%$search%'");
} else {
    $techs = mysqli_query($conn, "SELECT t.*, u.name, u.phone FROM technicians t 
                                  JOIN users u ON t.user_id = u.id");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FixHub — Find Technicians</title>
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
.nav-right {
    display: flex;
    align-items: center;
    gap: 16px;
}
.nav-user {
    font-size: 13px;
    color: rgba(255,255,255,0.5);
}
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
.nav-link.logout { color: rgba(255,255,255,0.4); }
.nav-link.logout:hover { color: #ff6b6b; background: rgba(255,68,68,0.08); }

.main { padding: 40px; max-width: 1200px; margin: 0 auto; }
.page-header { margin-bottom: 32px; }
.page-header h1 { font-size: 28px; font-weight: 800; margin-bottom: 6px; }
.page-header p { color: rgba(255,255,255,0.4); font-size: 14px; }

.search-bar {
    display: flex;
    gap: 12px;
    margin-bottom: 32px;
}
.search-wrap {
    flex: 1;
    position: relative;
}
.search-wrap i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,0.25);
}
.search-wrap input {
    width: 100%;
    padding: 13px 16px 13px 44px;
    background: #111111;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 10px;
    color: #fff;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    outline: none;
    transition: all 0.2s;
}
.search-wrap input:focus {
    border-color: #FF5A00;
    box-shadow: 0 0 0 3px rgba(255,90,0,0.1);
}
.search-wrap input::placeholder { color: rgba(255,255,255,0.2); }
.btn-search {
    padding: 13px 24px;
    background: linear-gradient(135deg, #FF5A00, #ff8c00);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-search:hover { opacity: 0.9; }

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}
.tech-card {
    background: #111111;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 14px;
    padding: 24px;
    transition: all 0.2s;
}
.tech-card:hover {
    border-color: rgba(255,90,0,0.3);
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
}
.tech-top {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
}
.tech-avatar {
    width: 52px;
    height: 52px;
    background: linear-gradient(135deg, rgba(255,90,0,0.2), rgba(255,90,0,0.1));
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
.tech-info h3 { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
.tech-skill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,90,0,0.1);
    border: 1px solid rgba(255,90,0,0.2);
    color: #FF5A00;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 100px;
}
.tech-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
}
.tech-detail {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: rgba(255,255,255,0.5);
}
.tech-detail i { color: #FF5A00; width: 14px; }
.tech-bio {
    font-size: 13px;
    color: rgba(255,255,255,0.4);
    line-height: 1.5;
    margin-bottom: 16px;
    padding-top: 16px;
    border-top: 1px solid rgba(255,255,255,0.06);
}
.rating { color: #f59e0b; font-size: 13px; font-weight: 600; }
.btn-book {
    display: block;
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #FF5A00, #ff8c00);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(255,90,0,0.3);
}
.btn-book:hover { opacity: 0.9; transform: translateY(-1px); }
.no-results {
    text-align: center;
    padding: 60px 20px;
    color: rgba(255,255,255,0.3);
}
.no-results i { font-size: 48px; margin-bottom: 16px; display: block; }
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
            <a href="technicians.php" class="nav-link active">Find Technicians</a>
            <a href="my_bookings.php" class="nav-link">My Bookings</a>
            <a href="logout.php" class="nav-link logout">Logout</a>
        </div>
    </div>
</nav>

<div class="main">
    <div class="page-header">
        <h1>Find a Technician</h1>
        <p>Browse available technicians in your area</p>
    </div>

    <form method="GET" class="search-bar">
        <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Search by name or skill..." value="<?php echo $search; ?>"/>
        </div>
        <button type="submit" class="btn-search">Search</button>
    </form>

    <div class="grid">
    <?php if(mysqli_num_rows($techs) > 0): ?>
        <?php while($tech = mysqli_fetch_assoc($techs)): ?>
        <div class="tech-card">
            <div class="tech-top">
                <div class="tech-avatar"><?php echo strtoupper(substr($tech['name'], 0, 1)); ?></div>
                <div class="tech-info">
                    <h3><?php echo $tech['name']; ?></h3>
                    <span class="tech-skill"><i class="fas fa-wrench"></i><?php echo $tech['skill']; ?></span>
                </div>
            </div>
            <div class="tech-details">
                <div class="tech-detail"><i class="fas fa-map-marker-alt"></i><?php echo $tech['location']; ?></div>
                <div class="tech-detail"><i class="fas fa-phone"></i><?php echo $tech['phone']; ?></div>
                <div class="tech-detail"><i class="fas fa-star" style="color:#f59e0b"></i><span class="rating"><?php echo $tech['rating']; ?> / 5.0</span></div>
            </div>
            <?php if($tech['bio']): ?>
            <div class="tech-bio"><?php echo $tech['bio']; ?></div>
            <?php endif; ?>
            <a href="booking.php?tech_id=<?php echo $tech['id']; ?>" class="btn-book">Book Now</a>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-results" style="grid-column:1/-1">
            <i class="fas fa-search"></i>
            <p>No technicians found.</p>
        </div>
    <?php endif; ?>
    </div>
</div>

</body>
</html>