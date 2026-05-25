<?php
// ============================================================
//  project-detail.php - Full Project Details Page
//  Shows all details of a single project using its ID from URL
//  URL example: project-detail.php?id=3
// ============================================================

include 'db.php';

// --- Step 1: Get the project ID from the URL ---
// $_GET['id'] reads the ?id=... part of the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // If no ID in URL, redirect to projects page
    header("Location: projects.php");
    exit();
}

$id = (int)$_GET['id']; // (int) converts to integer for safety

// --- Step 2: Fetch the project from database using the ID ---
// We use WHERE id = $id to get only that specific project
$query = "SELECT * FROM projects WHERE id = $id";
$result = mysqli_query($conn, $query);

// --- Step 3: Check if project exists ---
if (mysqli_num_rows($result) == 0) {
    // Project not found
    header("Location: projects.php");
    exit();
}

// --- Step 4: Get the project data ---
$project = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title']); ?> - CSE Repository</title>
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

<!-- ======================== PROJECT DETAIL ======================== -->
<div class="detail-wrapper">
    <div class="detail-card">

        <!-- ---- Detail Header (Navy background) ---- -->
        <div class="detail-header">
            <!-- Breadcrumb navigation -->
            <div class="breadcrumb">
                <a href="index.php">Home</a> &rsaquo;
                <a href="projects.php">Projects</a> &rsaquo;
                <?php echo htmlspecialchars($project['title']); ?>
            </div>

            <h1><?php echo htmlspecialchars($project['title']); ?></h1>

            <!-- Meta info chips -->
            <div class="detail-meta">
                <div class="meta-chip">
                    <span class="label">Student:</span>
                    <?php echo htmlspecialchars($project['student_name']); ?>
                </div>
                <div class="meta-chip">
                    <span class="label">Year:</span>
                    <?php echo $project['year']; ?>
                </div>
                <div class="meta-chip">
                    <span class="label">Category:</span>
                    <?php echo htmlspecialchars($project['category']); ?>
                </div>
                <div class="meta-chip">
                    <span class="label">Uploaded:</span>
                    <?php echo date('d M Y', strtotime($project['created_at'])); ?>
                </div>
            </div>
        </div>

        <!-- ---- Detail Body ---- -->
        <div class="detail-body">

            <!-- Description -->
            <div class="detail-section">
                <h3>📋 Project Description</h3>
                <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
            </div>

            <!-- Technologies -->
            <div class="detail-section">
                <h3>🛠️ Technologies Used</h3>
                <div class="card-tech" style="margin-top: 0.5rem;">
                    <?php
                    $techs = explode(',', $project['technology']);
                    foreach ($techs as $tech):
                        $tech = trim($tech);
                        if ($tech !== ''):
                    ?>
                        <span class="tech-tag" style="font-size:0.85rem; padding: 6px 14px;">
                            <?php echo htmlspecialchars($tech); ?>
                        </span>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </div>
            </div>

            <!-- GitHub Link (if provided) -->
            <?php if (!empty($project['github_link'])): ?>
            <div class="detail-section">
                <h3>🔗 GitHub Repository</h3>
                <a href="<?php echo htmlspecialchars($project['github_link']); ?>" 
                   target="_blank" 
                   class="github-link-box">
                    🐙 <?php echo htmlspecialchars($project['github_link']); ?>
                    <span style="margin-left:auto; opacity:0.5;">↗</span>
                </a>
                <p style="font-size:0.8rem; color: var(--text-light); margin-top:8px;">
                    * Opens in a new tab
                </p>
            </div>
            <?php else: ?>
            <div class="detail-section">
                <h3>🔗 GitHub Repository</h3>
                <p style="color: var(--text-light); font-style: italic;">No GitHub link provided.</p>
            </div>
            <?php endif; ?>

        </div>

        <!-- ---- Action Buttons ---- -->
        <div class="detail-actions">
            <a href="edit-project.php?id=<?php echo $project['id']; ?>" class="btn btn-edit">
                ✏️ Edit Project
            </a>

            <!-- Delete form -->
            <form method="POST" action="delete-project.php" style="display:inline;"
                  onsubmit="return confirmDelete('<?php echo addslashes(htmlspecialchars($project['title'])); ?>')">
                <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                <input type="hidden" name="redirect" value="projects.php">
                <button type="submit" class="btn btn-delete">🗑️ Delete Project</button>
            </form>

            <a href="projects.php" class="btn btn-outline" style="margin-left: auto;">
                ← Back to Projects
            </a>
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
