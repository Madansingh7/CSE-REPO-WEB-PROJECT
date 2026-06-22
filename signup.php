<?php
// ============================================================
//  signup.php - Student Registration Page
//  New students can create accounts here
// ============================================================

include 'auth.php';
include 'db.php';

// Redirect if already logged in
redirectIfLoggedIn();

$errorMessage = '';
$successMessage = '';

// --- Process signup form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $name       = trim($_POST['name'] ?? '');
    $usn        = trim($_POST['usn'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $semester   = trim($_POST['semester'] ?? '');
    $division   = trim($_POST['division'] ?? '');
    $password   = trim($_POST['password'] ?? '');
    $confirm_pw = trim($_POST['confirm_password'] ?? '');
    
    $errors = [];

    // --- Validation ---
    
    // Name validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    } else if (strlen($name) < 3) {
        $errors[] = "Name must be at least 3 characters long.";
    } else if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        $errors[] = "Name should contain only letters and spaces.";
    }

    // USN validation
    if (empty($usn)) {
        $errors[] = "USN (Unique Student Number) is required.";
    } else {
        $usnError = validateUSN($usn);
        if ($usnError) $errors[] = $usnError;
    }

    // Email validation
    if (empty($email)) {
        $errors[] = "Email address is required.";
    } else {
        $emailError = validateEmail($email);
        if ($emailError) $errors[] = $emailError;
    }

    // Phone validation
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } else {
        $phoneError = validatePhone($phone);
        if ($phoneError) $errors[] = $phoneError;
    }

    // Semester validation
    if (empty($semester)) {
        $errors[] = "Semester is required.";
    } else {
        $semError = validateSemester($semester);
        if ($semError) $errors[] = $semError;
    }

    // Division validation
    if (empty($division)) {
        $errors[] = "Division is required.";
    }

    // Password validation
    if (empty($password)) {
        $errors[] = "Password is required.";
    } else {
        $pwError = validatePassword($password);
        if ($pwError) $errors[] = $pwError;
    }

    // Confirm password
    if (empty($confirm_pw)) {
        $errors[] = "Please confirm your password.";
    } else if ($password !== $confirm_pw) {
        $errors[] = "Passwords do not match.";
    }

    // --- If no errors, save to database ---
    if (empty($errors)) {

        // Escape strings to prevent SQL injection
        $name     = mysqli_real_escape_string($conn, $name);
        $usn      = mysqli_real_escape_string($conn, $usn);
        $email    = mysqli_real_escape_string($conn, $email);
        $phone    = mysqli_real_escape_string($conn, $phone);
        $semester = (int)$semester;
        $division = mysqli_real_escape_string($conn, $division);

        // Hash password using bcrypt
        // password_hash() creates a secure hash of the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Check if USN already exists
        $checkUSN = "SELECT id FROM users WHERE usn = '$usn'";
        $checkResult = mysqli_query($conn, $checkUSN);
        if (mysqli_num_rows($checkResult) > 0) {
            $errors[] = "This USN is already registered.";
        } else {
            
            // Check if Email already exists
            $checkEmail = "SELECT id FROM users WHERE email = '$email'";
            $checkResult = mysqli_query($conn, $checkEmail);
            if (mysqli_num_rows($checkResult) > 0) {
                $errors[] = "This email is already registered.";
            } else {

                // Insert new user into database
                $insertQuery = "INSERT INTO users 
                                (name, usn, email, phone, semester, division, password, role)
                                VALUES 
                                ('$name', '$usn', '$email', '$phone', $semester, '$division', '$hashedPassword', 'student')";

                $insertResult = mysqli_query($conn, $insertQuery);

                if ($insertResult) {
                    // Get the new user ID
                    $newId = mysqli_insert_id($conn);
                    
                    // Set success message and redirect to login
                    $_SESSION['message'] = "✅ Account created successfully! Please login with your credentials.";
                    header("Location: login.php");
                    exit();
                } else {
                    $errors[] = "Registration failed: " . mysqli_error($conn);
                }
            }
        }
    }

    // Build error message if there are errors
    if (!empty($errors)) {
        $errorMessage = "❌ Signup failed:<br>" . implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - CSE Repository</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Signup page specific styles */
        .signup-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            background: var(--off-white);
        }

        .signup-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--card-shadow);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
        }

        .signup-header {
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            padding: 2.5rem 2rem;
            color: var(--white);
            text-align: center;
        }

        .signup-header h1 {
            font-family: 'Syne', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .signup-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
        }

        .signup-body {
            padding: 2.5rem 2rem;
        }

        .signup-footer {
            padding: 1.5rem 2rem;
            border-top: 1px solid var(--border);
            background: var(--off-white);
            text-align: center;
            font-size: 0.92rem;
        }

        .signup-footer a {
            color: var(--amber);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
        }

        .signup-footer a:hover {
            color: var(--amber-dark);
            text-decoration: underline;
        }

        .signup-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 480px) {
            .signup-form-row {
                grid-template-columns: 1fr;
            }
            .signup-card {
                border-radius: 8px;
            }
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
        <a href="login.php">Login</a>
        <a href="signup.php" class="btn-upload active">+ Sign Up</a>
    </div>
</nav>

<!-- ======================== SIGNUP FORM ======================== -->
<div class="signup-container">
    <div class="signup-card">

        <div class="signup-header">
            <h1>📝 Create Account</h1>
            <p>Join SDMCET's CSE Project Repository</p>
        </div>

        <div class="signup-body">

            <!-- Error Message -->
            <?php if ($errorMessage): ?>
                <div class="alert alert-error">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>

            <!-- Signup Form -->
            <form method="POST" action="" onsubmit="return validateSignupForm()">

                <!-- Full Name -->
                <div class="form-group">
                    <label for="name">Full Name <span>*</span></label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name"
                        placeholder="e.g., Rahul Sharma"
                        value="<?php echo htmlspecialchars($name ?? ''); ?>"
                        required
                    >
                    <div class="field-error" id="nameError"></div>
                </div>

                <!-- USN + Email -->
                <div class="signup-form-row">
                    <div class="form-group">
                        <label for="usn">USN <span>*</span></label>
                        <input 
                            type="text" 
                            id="usn" 
                            name="usn"
                            placeholder="e.g., 2sd24cs034"
                            value="<?php echo htmlspecialchars($usn ?? ''); ?>"
                            required
                        >
                        <div class="field-error" id="usnError"></div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span>*</span></label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email"
                            placeholder="your@email.com"
                            value="<?php echo htmlspecialchars($email ?? ''); ?>"
                            required
                        >
                        <div class="field-error" id="emailError"></div>
                    </div>
                </div>

                <!-- Phone + Semester -->
                <div class="signup-form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number <span>*</span></label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone"
                            placeholder="10-digit number"
                            value="<?php echo htmlspecialchars($phone ?? ''); ?>"
                            required
                        >
                        <div class="field-error" id="phoneError"></div>
                    </div>

                    <div class="form-group">
                        <label for="semester">Semester <span>*</span></label>
                        <select id="semester" name="semester" required>
                            <option value="">-- Select --</option>
                            <?php for ($i = 1; $i <= 8; $i++): ?>
                                <option value="<?php echo $i; ?>" 
                                    <?php echo (isset($semester) && $semester == $i) ? 'selected' : ''; ?>>
                                    Semester <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <div class="field-error" id="semesterError"></div>
                    </div>
                </div>

                <!-- Division -->
                <div class="form-group">
                    <label for="division">Division <span>*</span></label>
                    <select id="division" name="division" required>
                        <option value="">-- Select Division --</option>
                        <?php
                        $divisions = ['A', 'B', 'C', 'D'];
                        foreach ($divisions as $div):
                        ?>
                            <option value="<?php echo $div; ?>" 
                                <?php echo (isset($division) && $division == $div) ? 'selected' : ''; ?>>
                                Division <?php echo $div; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="field-error" id="divisionError"></div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password <span>*</span></label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        placeholder="Minimum 6 characters"
                        required
                    >
                    <div class="field-error" id="passwordError"></div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="confirm_password">Confirm Password <span>*</span></label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password"
                        placeholder="Re-enter your password"
                        required
                    >
                    <div class="field-error" id="confirmPasswordError"></div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    📝 Create Account
                </button>

            </form>

        </div>

        <div class="signup-footer">
            Already have an account? <a href="login.php">Login here →</a>
        </div>

    </div>
</div>

<!-- ======================== FOOTER ======================== -->
<footer>
    <p>SDMCET · CSE Project Repository &copy; <?php echo date('Y'); ?> · Built with <strong>PHP + MySQL</strong></p>
</footer>

<script>
    // Signup form validation
    function validateSignupForm() {
        let isValid = true;

        // Clear previous errors
        document.querySelectorAll('.field-error').forEach(el => {
            el.style.display = 'none';
        });
        document.querySelectorAll('.error-input').forEach(el => {
            el.classList.remove('error-input');
        });

        // Name
        const name = document.getElementById('name');
        if (!name.value.trim()) {
            showFieldError('nameError', 'Name is required.');
            name.classList.add('error-input');
            isValid = false;
        } else if (name.value.trim().length < 3) {
            showFieldError('nameError', 'Name must be at least 3 characters.');
            name.classList.add('error-input');
            isValid = false;
        }

        // USN
        const usn = document.getElementById('usn');
        if (!usn.value.trim()) {
            showFieldError('usnError', 'USN is required.');
            usn.classList.add('error-input');
            isValid = false;
        } else if (!/^[0-9]{1}[a-z]{2}[0-9]{2}[a-z]{2}[0-9]{3}$/i.test(usn.value.trim())) {
            showFieldError('usnError', 'USN format invalid. Example: 2sd24cs034');
            usn.classList.add('error-input');
            isValid = false;
        }

        // Email
        const email = document.getElementById('email');
        if (!email.value.trim()) {
            showFieldError('emailError', 'Email is required.');
            email.classList.add('error-input');
            isValid = false;
        } else if (!email.value.includes('@')) {
            showFieldError('emailError', 'Please enter a valid email.');
            email.classList.add('error-input');
            isValid = false;
        }

        // Phone
        const phone = document.getElementById('phone');
        const phoneDigits = phone.value.replace(/\D/g, '');
        if (!phone.value.trim()) {
            showFieldError('phoneError', 'Phone number is required.');
            phone.classList.add('error-input');
            isValid = false;
        } else if (phoneDigits.length !== 10) {
            showFieldError('phoneError', 'Phone must be 10 digits.');
            phone.classList.add('error-input');
            isValid = false;
        }

        // Semester
        const semester = document.getElementById('semester');
        if (!semester.value) {
            showFieldError('semesterError', 'Please select a semester.');
            semester.classList.add('error-input');
            isValid = false;
        }

        // Division
        const division = document.getElementById('division');
        if (!division.value) {
            showFieldError('divisionError', 'Please select a division.');
            division.classList.add('error-input');
            isValid = false;
        }

        // Password
        const password = document.getElementById('password');
        if (!password.value) {
            showFieldError('passwordError', 'Password is required.');
            password.classList.add('error-input');
            isValid = false;
        } else if (password.value.length < 6) {
            showFieldError('passwordError', 'Password must be at least 6 characters.');
            password.classList.add('error-input');
            isValid = false;
        }

        // Confirm Password
        const confirmPassword = document.getElementById('confirm_password');
        if (!confirmPassword.value) {
            showFieldError('confirmPasswordError', 'Please confirm your password.');
            confirmPassword.classList.add('error-input');
            isValid = false;
        } else if (password.value !== confirmPassword.value) {
            showFieldError('confirmPasswordError', 'Passwords do not match.');
            confirmPassword.classList.add('error-input');
            isValid = false;
        }

        return isValid;
    }

    function showFieldError(elementId, message) {
        const errorDiv = document.getElementById(elementId);
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
    }

    // Clear error on input
    document.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('error-input');
        });
        field.addEventListener('change', function() {
            this.classList.remove('error-input');
        });
    });
</script>

</body>
</html>
<?php mysqli_close($conn); ?>
