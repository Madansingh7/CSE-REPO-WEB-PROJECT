<?php
// ============================================================
//  upload.php - Upload New Project Page
//  Shows the form AND processes form submission
//  When user clicks submit, the page reloads with POST data
// ============================================================

include 'db.php';

// Variables to hold messages and keep form data on error
$successMessage = '';
$errorMessage   = '';

// --- Process form when it is submitted (POST request) ---
// $_SERVER['REQUEST_METHOD'] tells us if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Step 1: Read form data using $_POST ---
    // trim() removes extra spaces from start and end
    $title        = trim($_POST['title']        ?? '');
    $student_name = trim($_POST['student_name'] ?? '');
    $year         = trim($_POST['year']         ?? '');
    $category     = trim($_POST['category']     ?? '');
    $technology   = trim($_POST['technology']   ?? '');
    $description  = trim($_POST['description']  ?? '');
    $github_link  = trim($_POST['github_link']  ?? '');

    // --- Step 2: PHP-side validation (backup to JS validation) ---
    $errors = [];

    if (empty($title))        $errors[] = "Title is required.";
    if (empty($student_name)) $errors[] = "Student name is required.";
    if (empty($year) || !is_numeric($year) || $year < 2000 || $year > date('Y'))
        $errors[] = "Please enter a valid year (2000 - " . date('Y') . ").";
    if (empty($category))     $errors[] = "Please select a category.";
    if (empty($technology))   $errors[] = "Technology field is required.";
    if (empty($description) || strlen($description) < 20)
        $errors[] = "Description must be at least 20 characters.";

    // If a GitHub link is provided, check its format
    if (!empty($github_link) && !preg_match('/^https?:\/\/(www\.)?github\.com\/.+/', $github_link)) {
        $errors[] = "Please enter a valid GitHub URL (https://github.com/...).";
    }

    // --- Step 3: If no errors, save to database ---
    if (empty($errors)) {

        // Use mysqli_real_escape_string() to prevent SQL injection
        // This escapes special characters in strings before inserting
        $title        = mysqli_real_escape_string($conn, $title);
        $student_name = mysqli_real_escape_string($conn, $student_name);
        $year         = (int)$year; // Convert year to integer
        $category     = mysqli_real_escape_string($conn, $category);
        $technology   = mysqli_real_escape_string($conn, $technology);
        $description  = mysqli_real_escape_string($conn, $description);
        $github_link  = mysqli_real_escape_string($conn, $github_link);

        // Build the INSERT SQL query
        // INSERT INTO table (columns) VALUES (values)
        $insertQuery = "INSERT INTO projects 
                        (title, student_name, year, category, technology, description, github_link)
                        VALUES 
                        ('$title', '$student_name', $year, '$category', '$technology', '$description', '$github_link')";

        // Run the query
        $insertResult = mysqli_query($conn, $insertQuery);

        if ($insertResult) {
            // mysqli_insert_id() gets the ID of the newly inserted row
            $newId = mysqli_insert_id($conn);
            $successMessage = "✅ Project uploaded successfully! <a href='project-detail.php?id=$newId'>View Project →</a>";
            
            // Clear all variables so form resets
            $title = $student_name = $year = $category = $technology = $description = $github_link = '';
        } else {
            $errorMessage = "❌ Database error: " . mysqli_error($conn);
        }

    } else {
        // Show all validation errors joined together
        $errorMessage = "❌ Please fix the following errors:<br>" . implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Project - CSE Repository</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ======================== NAVIGATION ======================== -->
<nav>
    <div class="nav-brand">📚 <span>CSE</span> Repository</div>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="projects.php">Projects</a>
        <a href="upload.php" class="btn-upload active">+ Upload</a>
    </div>
</nav>

<!-- ======================== UPLOAD FORM ======================== -->
<div class="form-wrapper">
    <div class="form-card">

        <!-- Form header -->
        <div class="form-header">
            <h1>📤 Upload New Project</h1>
            <p>Fill in the details below to add your project to the repository</p>
        </div>

        <div class="form-body">

            <!-- Show success message -->
            <?php if ($successMessage): ?>
                <div class="alert alert-success">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>

            <!-- Show error message -->
            <?php if ($errorMessage): ?>
                <div class="alert alert-error">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>

            <!-- 
                THE FORM
                method="POST" = sends data securely via POST
                action="" = submits to the same page (upload.php)
                onsubmit="return validateProjectForm()" = runs JS validation first
            -->
            <form method="POST" action="" onsubmit="return validateProjectForm()">

                <!-- Row 1: Title (full width) -->
                <div class="form-group">
                    <label for="title">Project Title <span>*</span></label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        placeholder="e.g., Online Voting System"
                        maxlength="255"
                        value="<?php echo htmlspecialchars($title ?? ''); ?>"
                    >
                    <!-- Error message shown by JavaScript -->
                    <div class="field-error" id="titleError"></div>
                </div>

                <!-- Row 2: Student Name + Year -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="student_name">Student Name <span>*</span></label>
                        <input 
                            type="text" 
                            id="student_name" 
                            name="student_name"
                            placeholder="e.g., Rahul Sharma"
                            value="<?php echo htmlspecialchars($student_name ?? ''); ?>"
                        >
                        <div class="field-error" id="studentNameError"></div>
                    </div>

                    <div class="form-group">
                        <label for="year">Project Year <span>*</span></label>
                        <input 
                            type="number" 
                            id="year" 
                            name="year"
                            placeholder="e.g., 2024"
                            min="2000"
                            max="<?php echo date('Y'); ?>"
                            value="<?php echo htmlspecialchars($year ?? ''); ?>"
                        >
                        <div class="field-error" id="yearError"></div>
                    </div>
                </div>

                <!-- Row 3: Category + Technology -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Category <span>*</span></label>
                        <select id="category" name="category">
                            <option value="">-- Select Category --</option>
                            <?php
                            // List of categories to choose from
                            $categories = [
                                'Web Development',
                                'Mobile App',
                                'Database Management',
                                'Artificial Intelligence',
                                'Machine Learning',
                                'Data Science',
                                'Networking',
                                'Cyber Security',
                                'IoT (Internet of Things)',
                                'Game Development',
                                'Other'
                            ];
                            foreach ($categories as $cat):
                                // Keep previous selection after error
                                $selected = (isset($category) && $category === $cat) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $cat; ?>" <?php echo $selected; ?>>
                                    <?php echo $cat; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="field-error" id="categoryError"></div>
                    </div>

                    <div class="form-group">
                        <label for="technology">Technologies Used <span>*</span></label>
                        <input 
                            type="text" 
                            id="technology" 
                            name="technology"
                            placeholder="e.g., HTML, CSS, PHP, MySQL"
                            value="<?php echo htmlspecialchars($technology ?? ''); ?>"
                        >
                        <div class="field-error" id="technologyError"></div>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description">Project Description <span>*</span></label>
                    <textarea 
                        id="description" 
                        name="description"
                        placeholder="Describe what your project does, its features, and how it works... (minimum 20 characters)"
                        maxlength="2000"
                    ><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                    <div class="char-count" id="charCount">0 / 2000</div>
                    <div class="field-error" id="descriptionError"></div>
                </div>

                <!-- GitHub Link (optional) -->
                <div class="form-group">
                    <label for="github_link">GitHub Repository Link <em style="font-weight:400; color: var(--text-light);">(optional)</em></label>
                    <input 
                        type="url" 
                        id="github_link" 
                        name="github_link"
                        placeholder="https://github.com/yourusername/project-name"
                        value="<?php echo htmlspecialchars($github_link ?? ''); ?>"
                    >
                    <div class="field-error" id="githubError"></div>
                </div>

                <!-- Form Footer: Submit button -->
                <div class="form-footer" style="padding: 0; border: none; background: none; margin-top: 0.5rem;">
                    <button type="submit" class="btn btn-primary">
                        📤 Upload Project
                    </button>
                    <a href="projects.php" class="btn btn-outline">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- ======================== FOOTER ======================== -->
<footer>
    <p>SDMCET · CSE Project Repository &copy; <?php echo date('Y'); ?> · Built with <strong>PHP + MySQL</strong></p>
</footer>

<script src="script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
