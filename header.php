<?php
require_once 'config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo">🏥 Pharmacia</h1>
            <div class="clock" id="realTimeClock"></div>
            <ul class="nav-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <?php if(isAdmin()): ?>
                <li><a href="medicines.php">Medicines</a></li>
                <li><a href="add_medicine.php">Add Medicine</a></li>
                <li><a href="customers.php">Customers</a></li>
                <?php endif; ?>
                <li><a href="sales.php">Sales</a></li>
                <li><a href="make_sale.php">New Sale</a></li>
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            </ul>
            <div class="user-info">
                <span>👤 <?php echo $_SESSION['username']; ?></span>
                <span class="badge"><?php echo ucfirst($_SESSION['user_type']); ?></span>
            </div>
        </div>
    </nav>
    <div class="container">
        <script>
        // Real-time clock
        function updateClock() {
            const now = new Date();
            const options = { 
                weekday: 'short', 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit' 
            };
            document.getElementById('realTimeClock').textContent = now.toLocaleDateString('en-US', options);
        }
        setInterval(updateClock, 1000);
        updateClock();
        </script>