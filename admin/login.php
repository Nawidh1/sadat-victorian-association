<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$error = '';

// Ensure default admin user exists with correct password
try {
    $pdo = getDBConnection();
    
    // Check if admin_users table exists, create if not
    try {
        $pdo->query("SELECT 1 FROM admin_users LIMIT 1");
    } catch (Exception $e) {
        // Table doesn't exist, create it
        $pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
    
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT id, password_hash FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $admin_user = $stmt->fetch();
    
    if (!$admin_user) {
        // Create default admin user (password: admin123)
        $default_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $insert_stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)");
        $insert_stmt->execute(['admin', $default_hash]);
    } else {
        // Verify the password hash works, if not update it
        if (!password_verify('admin123', $admin_user['password_hash'])) {
            // Update with correct hash
            $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE username = 'admin'");
            $update_stmt->execute([$new_hash]);
        }
    }
} catch (Exception $e) {
    error_log("Error checking/creating admin user: " . $e->getMessage());
    // Continue anyway - might be database connection issue
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_user_id'] = $user['id'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid username or password';
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $error = 'Login failed. Please check database connection.';
        }
    }
}

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Sadat Victorian Association</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
            padding: 2rem;
        }
        .login-box {
            background: white;
            padding: 3.5rem;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 480px;
            width: 100%;
            animation: slideUp 0.5s ease-out;
            border-top: 4px solid var(--accent-color);
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-box h1 {
            text-align: center;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 700;
        }
        .login-box p {
            text-align: center;
            color: var(--text-light);
            margin-bottom: 2.5rem;
            font-size: 1.1rem;
        }
        .error-message {
            background: linear-gradient(135deg, #fee 0%, #fdd 100%);
            color: #c33;
            padding: 1.2rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #c33;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        .error-message::before {
            content: '⚠';
            font-size: 1.5rem;
        }
        .login-form .form-group {
            margin-bottom: 1.5rem;
        }
        .login-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-dark);
        }
        .login-form input {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }
        .login-form input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(26, 71, 42, 0.08);
            transform: translateY(-1px);
        }
        .login-form button {
            width: 100%;
            padding: 1.1rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(26, 71, 42, 0.3);
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }
        .login-form button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        .login-form button:hover::before {
            width: 400px;
            height: 400px;
        }
        .login-form button:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(26, 71, 42, 0.4);
        }
        .login-form button:active {
            transform: translateY(-1px);
        }
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        .back-link a {
            color: var(--primary-color);
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Admin Login</h1>
            <p>Sadat Victorian Association</p>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit">Login</button>
            </form>
            
            <div class="back-link">
                <a href="../index.php">← Back to Website</a>
            </div>
        </div>
    </div>
</body>
</html>
