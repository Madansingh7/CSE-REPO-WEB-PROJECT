<?php
// ============================================================
//  index.php - Home Page
//  Shows hero section, stats, and recent projects from database
// ============================================================

include 'db.php'; // Connect to the database

// --- Fetch total project count ---
$countResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM projects");
$countRow = mysqli_fetch_assoc($countResult);
$totalProjects = $countRow['total'];

// --- Fetch total unique students ---
$studentResult = mysqli_query($conn, "SELECT COUNT(DISTINCT student_name) AS total FROM projects");
$studentRow = mysqli_fetch_assoc($studentResult);
$totalStudents = $studentRow['total'];

// --- Fetch total unique categories ---
$catResult = mysqli_query($conn, "SELECT COUNT(DISTINCT category) AS total FROM projects");
$catRow = mysqli_fetch_assoc($catResult);
$totalCategories = $catRow['total'];

// --- Fetch 6 most recent projects ---
// ORDER BY created_at DESC = newest first
// LIMIT 6 = only show 6 projects
$recentQuery = "SELECT * FROM projects ORDER BY created_at DESC LIMIT 6";
$recentResult = mysqli_query($conn, $recentQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSE Project Repository - Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ======================== NAVIGATION ======================== -->
<nav>
    <div class="nav-brand">
        📚 <span>CSE</span> Repository
    </div>
    <div class="nav-links">
        <a href="index.php" class="active">Home</a>
        <a href="projects.php">Projects</a>
        <a href="upload.php" class="btn-upload">+ Upload</a>
    </div>
</nav>

<!-- ======================== HERO SECTION ======================== -->
<section class="hero">

    <!-- SDMCET Logo -->
    <div class="college-logo">
        <a href="https://sdmcet.ac.in" target="_blank">
        <img src="sdmcet.jpg" alt="SDMCET Logo">
    </a>

    </div>

    <div class="hero-badge">CSE Department · Project Showcase</div>

    <h1>Student <span>Project</span><br>Repository</h1>

    <p>
        Explore, share, and celebrate student innovations.
        Find projects by category, technology, or student name.
    </p>

    <div class="hero-actions">
        <a href="projects.php" class="btn btn-primary">Browse Projects →</a>
        <a href="upload.php" class="btn btn-secondary">+ Upload Your Project</a>
    </div>

</section>
<!-- ======================== STATS BAR ======================== -->
<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-number"><?php echo $totalProjects; ?><span>+</span></div>
        <div class="stat-label">Projects Uploaded</div>
    </div>
    <div class="stat-item">
        <div class="stat-number"><?php echo $totalStudents; ?><span>+</span></div>
        <div class="stat-label">Students</div>
    </div>
    <div class="stat-item">
        <div class="stat-number"><?php echo $totalCategories; ?><span>+</span></div>
        <div class="stat-label">Categories</div>
    </div>
</div>

<!-- ======================== RECENT PROJECTS ======================== -->
<div class="section">
    <div class="section-header">
        <div>
            <h2 class="section-title">Recent Projects</h2>
            <p class="section-subtitle">Latest submissions from our students</p>
        </div>
        <a href="projects.php" class="btn btn-outline btn-sm">View All →</a>
    </div>

    <?php if (mysqli_num_rows($recentResult) > 0): ?>
        <div class="projects-grid">
            <?php while ($project = mysqli_fetch_assoc($recentResult)): ?>
                <!-- Each project card -->
                <div class="project-card" data-category="<?php echo htmlspecialchars($project['category']); ?>">
                    
                    <!-- Card top bar (navy background) -->
                    <div class="card-header">
                        <span class="card-category"><?php echo htmlspecialchars($project['category']); ?></span>
                        <span class="card-year"><?php echo $project['year']; ?></span>
                    </div>

                    <!-- Card content -->
                    <div class="card-body">
                        <h3 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                        <p class="card-student">👤 <?php echo htmlspecialchars($project['student_name']); ?></p>
                        <p class="card-desc"><?php echo htmlspecialchars($project['description']); ?></p>

                        <!-- Technology tags -->
                        <div class="card-tech">
                            <?php
                            // Split technologies by comma and show each as a tag
                            $techs = explode(',', $project['technology']);
                            foreach ($techs as $tech):
                                $tech = trim($tech);
                                if ($tech !== ''):
                            ?>
                                <span class="tech-tag"><?php echo htmlspecialchars($tech); ?></span>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </div>
                    </div>

                    <!-- Card footer with action buttons -->
                    <div class="card-footer">
                        <a href="project-detail.php?id=<?php echo $project['id']; ?>" class="btn btn-primary btn-sm">
                            View Details
                        </a>
                        <a href="edit-project.php?id=<?php echo $project['id']; ?>" class="btn btn-edit btn-sm">
                            ✏️ Edit
                        </a>
                        <!-- Delete form with confirmation -->
                        <form method="POST" action="delete-project.php" style="display:inline;"
                              onsubmit="return confirmDelete('<?php echo addslashes(htmlspecialchars($project['title'])); ?>')">
                            <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                            <input type="hidden" name="redirect" value="index.php">
                            <button type="submit" class="btn btn-delete btn-sm">🗑️ Delete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    <?php else: ?>
        <!-- Show this when no projects exist yet -->
        <div class="empty-state">
            <div class="empty-icon">📂</div>
            <h3>No Projects Yet</h3>
            <p>Be the first to upload a project!</p>
            <br>
            <a href="upload.php" class="btn btn-primary">+ Upload First Project</a>
        </div>
    <?php endif; ?>
</div>

<!-- ======================== FOOTER ======================== -->
<footer>
    <p>SDMCET · CSE Project Repository &copy; <?php echo date('Y'); ?> · Built by <strong>Madan & yash</strong></p>
</footer>

<script src="script.js"></script>
</body>
</html>
<?php
// Close the database connection
mysqli_close($conn);
?>
