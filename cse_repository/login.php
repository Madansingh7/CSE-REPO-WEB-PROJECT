<?php
// ============================================================
//  login.php - Student & Admin Login Page
//  Users can login using email and password
// ============================================================

include 'auth.php';
include 'db.php';

// Redirect if already logged in
redirectIfLoggedIn();

$errorMessage = '';
$successMessage = getAndClearMessage();

// --- Process login form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $errors   = [];

    // --- Validation ---
    if (empty($email)) {
        $errors[] = "Email is required.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // --- If no validation errors, check database ---
    if (empty($errors)) {
        
        // Escape email to prevent SQL injection
        $email = mysqli_real_escape_string($conn, $email);
        
        // Query to find user by email
        $query = "SELECT id, name, email, password, role FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            // Verify password using bcrypt
            // password_verify() compares the plain password with the hashed one
            if (password_verify($password, $user['password'])) {
                
                // ✅ Login successful! Set session variables
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin-dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
                
            } else {
                // Password is wrong
                $errors[] = "Email or password is incorrect.";
            }
        } else {
            // User not found
            $errors[] = "Email or password is incorrect.";
        }
    }

    // Build error message if there are errors
    if (!empty($errors)) {
        $errorMessage = "❌ Login failed:<br>" . implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CSE Repository</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Login page specific styles */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }

        .login-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--card-shadow);
            max-width: 420px;
            width: 100%;
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            padding: 2.5rem 2rem;
            color: var(--white);
            text-align: center;
        }

        .login-header h1 {
            font-family: 'Syne', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
        }

        .login-body {
            padding: 2.5rem 2rem;
        }

        .login-footer {
            padding: 1.5rem 2rem;
            border-top: 1px solid var(--border);
            background: var(--off-white);
            text-align: center;
            font-size: 0.92rem;
        }

        .login-footer a {
            color: var(--amber);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
        }

        .login-footer a:hover {
            color: var(--amber-dark);
            text-decoration: underline;
        }

        .login-divider {
            text-align: center;
            margin: 1.5rem 0;
            color: var(--text-light);
            font-size: 0.85rem;
        }

        .demo-info {
            background: rgba(245, 166, 35, 0.1);
            border: 1px solid rgba(245, 166, 35, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            color: var(--text-mid);
        }

        .demo-info strong {
            color: var(--navy);
            display: block;
            margin-bottom: 0.5rem;
        }

        .demo-info code {
            background: var(--white);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: var(--navy);
            font-weight: 600;
        }
    </style>
</head>
<body>

<!-- ======================== NAVIGATION ======================== -->
<nav>
    <div class="nav-brand">📚 <span>CSE</span> Repository</div>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="projects.php">Projects</a>
        <a href="login.php" class="active">Login</a>
        <a href="signup.php" class="btn-upload">+ Sign Up</a>
    </div>
</nav>

<!-- ======================== LOGIN FORM ======================== -->
<div class="login-container">
    <div class="login-card">

        <div class="login-header">
            <h1>🔐 Login</h1>
            <p>Welcome back! Enter your credentials.</p>
        </div>

        <div class="login-body">

            <!-- Success Message -->
            <?php if ($successMessage): ?>
                <div class="alert alert-success">
                    ✅ <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if ($errorMessage): ?>
                <div class="alert alert-error">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>

            <!-- Demo Info -->
            <div class="demo-info">
                <strong>📌 Demo Credentials (Admin):</strong>
                Email: <code>admin@sdmcet.edu</code><br>
                Password: <code>admin123</code>
            </div>

            <!-- Login Form -->
            <form method="POST" action="" onsubmit="return validateLoginForm()">

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address <span>*</span></label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        placeholder="your.email@example.com"
                        value="<?php echo htmlspecialchars($email ?? ''); ?>"
                        required
                    >
                    <div class="field-error" id="emailError"></div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password <span>*</span></label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        placeholder="Enter your password"
                        required
                    >
                    <div class="field-error" id="passwordError"></div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    🔓 Login
                </button>

            </form>

        </div>

        <div class="login-footer">
            Don't have an account? <a href="signup.php">Sign up here →</a>
        </div>

    </div>
</div>

<!-- ======================== FOOTER ======================== -->
<footer>
    <p>SDMCET · CSE Project Repository &copy; <?php echo date('Y'); ?> · Built with <strong>PHP + MySQL</strong></p>
</footer>

<script>
    // Simple login form validation
    function validateLoginForm() {
        let isValid = true;

        const email = document.getElementById('email');
        const password = document.getElementById('password');

        // Clear previous errors
        document.getElementById('emailError').style.display = 'none';
        document.getElementById('passwordError').style.display = 'none';

        // Validate email
        if (!email.value.trim()) {
            document.getElementById('emailError').textContent = 'Email is required.';
            document.getElementById('emailError').style.display = 'block';
            email.classList.add('error-input');
            isValid = false;
        } else if (!email.value.includes('@')) {
            document.getElementById('emailError').textContent = 'Please enter a valid email.';
            document.getElementById('emailError').style.display = 'block';
            email.classList.add('error-input');
            isValid = false;
        } else {
            email.classList.remove('error-input');
        }

        // Validate password
        if (!password.value.trim()) {
            document.getElementById('passwordError').textContent = 'Password is required.';
            document.getElementById('passwordError').style.display = 'block';
            password.classList.add('error-input');
            isValid = false;
        } else {
            password.classList.remove('error-input');
        }

        return isValid;
    }

    // Clear error on input
    document.getElementById('email').addEventListener('input', function() {
        this.classList.remove('error-input');
        document.getElementById('emailError').style.display = 'none';
    });

    document.getElementById('password').addEventListener('input', function() {
        this.classList.remove('error-input');
        document.getElementById('passwordError').style.display = 'none';
    });
</script>

</body>
</html>
<?php mysqli_close($conn); ?>
