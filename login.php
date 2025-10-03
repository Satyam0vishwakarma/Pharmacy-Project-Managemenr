<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);
    $user_type = $_POST['user_type'];
    
    if ($user_type === 'admin') {
        $query = "SELECT * FROM admin WHERE username = ? AND password = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_type'] = 'admin';
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid admin credentials!";
        }
    } else {
        $query = "SELECT * FROM pharmacist WHERE username = ? AND password = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['name'];
            $_SESSION['user_type'] = 'pharmacist';
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid pharmacist credentials!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pharmacy Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-box">
            <h1>🏥 Pharmacia</h1>
            <h2>Login</h2>
            <?php if($error): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required autocomplete="current-password">
                </div>
                <div class="form-group">
                    <label>Login As</label>
                    <select name="user_type" required>
                        <option value="admin">Admin</option>
                        <option value="pharmacist">Pharmacist</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Login</button>
            </form>
           
        </div>
    </div>
</body>
</html>