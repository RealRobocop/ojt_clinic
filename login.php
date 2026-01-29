<?php
/**
 * MediCare - Doctor Login Page
 * Clinic Management System
 */

session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['doctor_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/db.php';
    
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        try {
            // Query doctor account
            $stmt = $pdo->prepare("SELECT * FROM doctor_accounts WHERE username = ?");
            $stmt->execute([$username]);
            $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($doctor) {
                // For testing: direct password comparison (plain text)
                // In production, use: password_verify($password, $doctor['password'])
                if ($password === $doctor['password']) {
                    // Set session
                    $_SESSION['doctor_id'] = $doctor['id'];
                    $_SESSION['doctor_username'] = $doctor['username'];
                    $_SESSION['doctor_name'] = $doctor['firstName'] . ' ' . $doctor['lastName'];
                    
                    // Update last login
                    $updateStmt = $pdo->prepare("UPDATE doctor_accounts SET lastLogin = CURRENT_TIMESTAMP WHERE id = ?");
                    $updateStmt->execute([$doctor['id']]);
                    
                    // Redirect to dashboard
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Invalid username or password';
                }
            } else {
                $error = 'Invalid username or password';
            }
        } catch (PDOException $e) {
            $error = 'Database error occurred';
            error_log('Login Error: ' . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediCare - Doctor Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1006ba 0%, #1a2a47 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            padding: 3rem;
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #d4af37, #e6c560);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            color: #1a2a47;
            margin: 0 auto 1rem;
        }

        .logo-section h1 {
            font-size: 2rem;
            color: #1a2a47;
            margin-bottom: 0.5rem;
        }

        .logo-section p {
            color: #6b7280;
            font-size: 0.95rem;
        }

        .form-section {
            margin-top: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: #1a2a47;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.875rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #d4af37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        .error-message {
            background: #fee2e2;
            color: #dc2626;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: none;
            font-size: 0.95rem;
            border-left: 4px solid #dc2626;
        }

        .error-message.show {
            display: block;
            animation: shake 0.3s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .login-btn {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #d4af37, #e6c560);
            color: #1a2a47;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            color: #6b7280;
            margin-top: 1rem;
        }

        .remember-me input {
            width: auto;
            margin: 0;
            padding: 0;
            border: none;
            cursor: pointer;
        }

        .help-text {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }

        .help-text h3 {
            color: #1a2a47;
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
        }

        .credentials {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            color: #4b5563;
            border-left: 4px solid #d4af37;
        }

        .credentials div {
            margin-bottom: 0.5rem;
        }

        .credentials strong {
            color: #1a2a47;
        }

        @media (max-width: 600px) {
            .login-container {
                margin: 1rem;
                padding: 2rem;
            }

            .logo-section h1 {
                font-size: 1.75rem;
            }

            .logo-icon {
                width: 50px;
                height: 50px;
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <div class="logo-icon">M</div>
            <h1>MediCare</h1>
            <p>Doctor Login Portal</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="error-message show">
            ❌ <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="form-section">
            <div class="form-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Enter your username"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password"
                    required
                >
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember" style="margin: 0; font-weight: 400;">Remember me</label>
            </div>

            <button type="submit" class="login-btn">
                🔓 Login to Dashboard
            </button>
        </form>

        <div class="help-text">
            <h3>📝 Demo Credentials</h3>
            <div class="credentials">
                <div><strong>Username:</strong> Doctor1</div>
                <div><strong>Password:</strong> Test123</div>
            </div>
        </div>
    </div>

    <script>
        // Remove error message after 5 seconds
        const errorMsg = document.querySelector('.error-message.show');
        if (errorMsg) {
            setTimeout(() => {
                errorMsg.style.display = 'none';
            }, 5000);
        }

        // Prevent form submission with empty fields
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!username || !password) {
                e.preventDefault();
                alert('Please fill in all fields');
            }
        });
    </script>
</body>
</html>
