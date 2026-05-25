<?php
// ============================================================
//  projects.php - All Projects Page
//  Shows all projects from database with search and filter
// ============================================================

include 'db.php'; // Connect to database

// --- Fetch all DISTINCT categories for the filter dropdown ---
$catQuery = "SELECT DISTINCT category FROM projects ORDER BY category ASC";
$catResult = mysqli_query($conn, $catQuery);

// --- Fetch ALL projects (newest first) ---
$projectQuery = "SELECT * FROM projects ORDER BY created_at DESC";
$projectResult = mysqli_query($conn, $projectQuery);
$totalProjects = mysqli_num_rows($projectResult);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Projects - CSE Repository</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ======================== NAVIGATION ======================== -->
<nav>
    <div class="nav-brand">📚 <span>CSE</span> Repository</div>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="projects.php" class="active">Projects</a>
        <a href="upload.php" class="btn-upload">+ Upload</a>
    </div>
</nav>

<!-- ======================== PAGE HEADER ======================== -->
<section class="hero" style="padding: 50px 2rem 60px;">
    <h1 style="font-size: clamp(1.6rem, 4vw, 2.5rem);">All <span>Projects</span></h1>
    <p>Browse the complete collection of student projects</p>
</section>

<!-- ======================== PROJECTS SECTION ======================== -->
<div class="section">

    <!-- Show delete success/fail message if redirected from delete-project.php -->
    <?php if (isset($_GET['deleted'])): ?>
        <?php if ($_GET['deleted'] == '1'): ?>
            <div class="alert alert-success">🗑️ Project deleted successfully.</div>
        <?php else: ?>
            <div class="alert alert-error">❌ Could not delete project. Please try again.</div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- SEARCH & FILTER BAR -->
    <!-- Note: Search and filter work using JavaScript (see script.js) -->
    <div class="filter-bar">
        <!-- Search Box -->
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input 
                type="text" 
                id="searchInput" 
                placeholder="Search by title, student, technology..."
                autocomplete="off"
            >
        </div>

        <!-- Category Filter Dropdown -->
        <select id="categoryFilter" class="filter-select">
            <option value="">All Categories</option>
            <?php
            // Reset and reuse catResult to populate dropdown
            mysqli_data_seek($catResult, 0);
            while ($cat = mysqli_fetch_assoc($catResult)):
            ?>
                <option value="<?php echo htmlspecialchars($cat['category']); ?>">
                    <?php echo htmlspecialchars($cat['category']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <!-- Results count (updated by JavaScript) -->
        <span class="results-count" id="resultsCount">
            <?php echo $totalProjects; ?> project(s) found
        </span>
    </div>

    <!-- PROJECTS GRID -->
    <?php if ($totalProjects > 0): ?>

        <div class="projects-grid" id="projectsGrid">
            <?php while ($project = mysqli_fetch_assoc($projectResult)): ?>

                <!-- Each card has data-category for JS filtering -->
                <div class="project-card" data-category="<?php echo htmlspecialchars($project['category']); ?>">

                    <!-- Top bar -->
                    <div class="card-header">
                        <span class="card-category"><?php echo htmlspecialchars($project['category']); ?></span>
                        <span class="card-year"><?php echo $project['year']; ?></span>
                    </div>

                    <!-- Body -->
                    <div class="card-body">
                        <h3 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                        <p class="card-student">👤 <?php echo htmlspecialchars($project['student_name']); ?></p>
                        <p class="card-desc"><?php echo htmlspecialchars($project['description']); ?></p>

                        <!-- Technology tags -->
                        <div class="card-tech">
                            <?php
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

                    <!-- Footer: action buttons -->
                    <div class="card-footer">
                        <a href="project-detail.php?id=<?php echo $project['id']; ?>" class="btn btn-primary btn-sm">
                            View Details
                        </a>
                        <a href="edit-project.php?id=<?php echo $project['id']; ?>" class="btn btn-edit btn-sm">
                            ✏️ Edit
                        </a>
                        <!-- Delete button with JS confirm() -->
                        <form method="POST" action="delete-project.php" style="display:inline;"
                              onsubmit="return confirmDelete('<?php echo addslashes(htmlspecialchars($project['title'])); ?>')">
                            <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                            <input type="hidden" name="redirect" value="projects.php">
                            <button type="submit" class="btn btn-delete btn-sm">🗑️</button>
                        </form>
                    </div>

                </div>
            <?php endwhile; ?>
        </div>

        <!-- Empty state (shown by JS when search has no results) -->
        <div id="emptyState" class="empty-state" style="display:none;">
            <div class="empty-icon">🔍</div>
            <h3>No Results Found</h3>
            <p>Try different search terms or clear the filter.</p>
        </div>

    <?php else: ?>
        <!-- No projects in database at all -->
        <div class="empty-state">
            <div class="empty-icon">📂</div>
            <h3>No Projects Yet</h3>
            <p>The repository is empty. Be the first to upload a project!</p>
            <br>
            <a href="upload.php" class="btn btn-primary">+ Upload First Project</a>
        </div>
    <?php endif; ?>

</div>

<!-- ======================== FOOTER ======================== -->
<footer>
    <p>SDMCET · CSE Project Repository &copy; <?php echo date('Y'); ?> · Built with <strong>PHP + MySQL</strong></p>
</footer>

<script src="script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
