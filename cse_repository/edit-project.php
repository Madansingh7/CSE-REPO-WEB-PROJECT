<?php
// ============================================================
//  edit-project.php - Edit Existing Project Page
//  1. First, loads existing project data to pre-fill the form
//  2. When form is submitted, runs UPDATE query to save changes
//  URL: edit-project.php?id=3
// ============================================================

include 'db.php';

// --- Step 1: Get ID from URL ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: projects.php");
    exit();
}

$id = (int)$_GET['id'];

// Variables for messages
$successMessage = '';
$errorMessage   = '';

// --- Step 2: Handle form submission (when user clicks Save) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Read submitted form data
    $title        = trim($_POST['title']        ?? '');
    $student_name = trim($_POST['student_name'] ?? '');
    $year         = trim($_POST['year']         ?? '');
    $category     = trim($_POST['category']     ?? '');
    $technology   = trim($_POST['technology']   ?? '');
    $description  = trim($_POST['description']  ?? '');
    $github_link  = trim($_POST['github_link']  ?? '');
    $id           = (int)($_POST['id']          ?? 0); // ID from hidden field

    // PHP validation
    $errors = [];
    if (empty($title))        $errors[] = "Title is required.";
    if (empty($student_name)) $errors[] = "Student name is required.";
    if (empty($year) || !is_numeric($year) || $year < 2000 || $year > date('Y'))
        $errors[] = "Year must be between 2000 and " . date('Y') . ".";
    if (empty($category))     $errors[] = "Category is required.";
    if (empty($technology))   $errors[] = "Technology field is required.";
    if (empty($description) || strlen($description) < 20)
        $errors[] = "Description must be at least 20 characters.";
    if (!empty($github_link) && !preg_match('/^https?:\/\/(www\.)?github\.com\/.+/', $github_link))
        $errors[] = "Please enter a valid GitHub URL.";

    // If no errors, update the database
    if (empty($errors)) {

        // Escape all string values to prevent SQL injection
        $title        = mysqli_real_escape_string($conn, $title);
        $student_name = mysqli_real_escape_string($conn, $student_name);
        $year         = (int)$year;
        $category     = mysqli_real_escape_string($conn, $category);
        $technology   = mysqli_real_escape_string($conn, $technology);
        $description  = mysqli_real_escape_string($conn, $description);
        $github_link  = mysqli_real_escape_string($conn, $github_link);

        // UPDATE query: changes existing record in database
        // SET column = 'value' specifies what to change
        // WHERE id = $id ensures we only update THIS project
        $updateQuery = "UPDATE projects SET
                            title        = '$title',
                            student_name = '$student_name',
                            year         = $year,
                            category     = '$category',
                            technology   = '$technology',
                            description  = '$description',
                            github_link  = '$github_link'
                        WHERE id = $id";

        $updateResult = mysqli_query($conn, $updateQuery);

        if ($updateResult) {
            $successMessage = "✅ Project updated successfully!";
        } else {
            $errorMessage = "❌ Update failed: " . mysqli_error($conn);
        }
    } else {
        $errorMessage = "❌ Please fix these errors:<br>" . implode('<br>', $errors);
    }
}

// --- Step 3: Fetch current project data to fill the form ---
// This runs AFTER potential update so form shows latest data
$fetchQuery  = "SELECT * FROM projects WHERE id = $id";
$fetchResult = mysqli_query($conn, $fetchQuery);

if (mysqli_num_rows($fetchResult) == 0) {
    // Project not found
    header("Location: projects.php");
    exit();
}

$project = mysqli_fetch_assoc($fetchResult);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project - CSE Repository</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ======================== NAVIGATION ======================== -->
<nav>
    <div class="nav-brand">📚 <span>CSE</span> Repository</div>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="projects.php">Projects</a>
        <a href="upload.php" class="btn-upload">+ Upload</a>
    </div>
</nav>

<!-- ======================== EDIT FORM ======================== -->
<div class="form-wrapper">
    <div class="form-card">

        <div class="form-header">
            <h1>✏️ Edit Project</h1>
            <p>Update the details for: <strong><?php echo htmlspecialchars($project['title']); ?></strong></p>
        </div>

        <div class="form-body">

            <!-- Success / Error Messages -->
            <?php if ($successMessage): ?>
                <div class="alert alert-success">
                    <?php echo $successMessage; ?>
                    &nbsp;&nbsp;
                    <a href="project-detail.php?id=<?php echo $project['id']; ?>" 
                       style="text-decoration:underline; font-weight:600;">View Project →</a>
                </div>
            <?php endif; ?>

            <?php if ($errorMessage): ?>
                <div class="alert alert-error">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>

            <!-- Edit Form - identical structure to upload form -->
            <!-- action="" submits to the same page -->
            <form method="POST" action="" onsubmit="return validateProjectForm()">

                <!-- Hidden input to pass the project ID when form is submitted -->
                <!-- This is how PHP knows WHICH project to update -->
                <input type="hidden" name="id" value="<?php echo $project['id']; ?>">

                <!-- Title -->
                <div class="form-group">
                    <label for="title">Project Title <span>*</span></label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title"
                        maxlength="255"
                        value="<?php echo htmlspecialchars($project['title']); ?>"
                    >
                    <div class="field-error" id="titleError"></div>
                </div>

                <!-- Student Name + Year -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="student_name">Student Name <span>*</span></label>
                        <input 
                            type="text" 
                            id="student_name" 
                            name="student_name"
                            value="<?php echo htmlspecialchars($project['student_name']); ?>"
                        >
                        <div class="field-error" id="studentNameError"></div>
                    </div>

                    <div class="form-group">
                        <label for="year">Project Year <span>*</span></label>
                        <input 
                            type="number" 
                            id="year" 
                            name="year"
                            min="2000"
                            max="<?php echo date('Y'); ?>"
                            value="<?php echo $project['year']; ?>"
                        >
                        <div class="field-error" id="yearError"></div>
                    </div>
                </div>

                <!-- Category + Technology -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Category <span>*</span></label>
                        <select id="category" name="category">
                            <option value="">-- Select Category --</option>
                            <?php
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
                                // Pre-select the current project's category
                                $selected = ($project['category'] === $cat) ? 'selected' : '';
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
                            value="<?php echo htmlspecialchars($project['technology']); ?>"
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
                        maxlength="2000"
                    ><?php echo htmlspecialchars($project['description']); ?></textarea>
                    <div class="char-count" id="charCount">
                        <?php echo strlen($project['description']); ?> / 2000
                    </div>
                    <div class="field-error" id="descriptionError"></div>
                </div>

                <!-- GitHub Link -->
                <div class="form-group">
                    <label for="github_link">GitHub Repository Link <em style="font-weight:400; color:var(--text-light);">(optional)</em></label>
                    <input 
                        type="url" 
                        id="github_link" 
                        name="github_link"
                        placeholder="https://github.com/username/repo"
                        value="<?php echo htmlspecialchars($project['github_link']); ?>"
                    >
                    <div class="field-error" id="githubError"></div>
                </div>

                <!-- Buttons -->
                <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:0.5rem;">
                    <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                    <a href="project-detail.php?id=<?php echo $project['id']; ?>" class="btn btn-outline">Cancel</a>
                    <!-- Delete button with confirm dialog -->
                    <form method="POST" action="delete-project.php" style="display:inline; margin-left:auto;"
                          onsubmit="return confirmDelete('<?php echo addslashes(htmlspecialchars($project['title'])); ?>')">
                        <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                        <input type="hidden" name="redirect" value="projects.php">
                        <button type="submit" class="btn btn-delete">🗑️ Delete Project</button>
                    </form>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- ======================== FOOTER ======================== -->
<footer>
    <p>CSE Project Repository &copy; <?php echo date('Y'); ?> · Built with <strong>PHP + MySQL</strong></p>
</footer>

<script src="script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
