<?php
// ============================================================
//  dashboard.php - Student Dashboard
//  Shows logged-in student's profile and their projects
// ============================================================

include 'auth.php';
include 'db.php';

// Require login
requireLogin();

// Get current user's ID
$userId = getCurrentUserId();

// --- Fetch user details ---
$userQuery = "SELECT * FROM users WHERE id = $userId";
$userResult = mysqli_query($conn, $userQuery);
$user = mysqli_fetch_assoc($userResult);

// --- Fetch user's projects ---
$projectQuery = "SELECT * FROM projects WHERE user_id = $userId ORDER BY created_at DESC";
$projectResult = mysqli_query($conn, $projectQuery);
$totalProjects = mysqli_num_rows($projectResult);

// --- Fetch total categories (for stats) ---
$catQuery = "SELECT COUNT(DISTINCT category) as total FROM projects WHERE user_id = $userId";
$catResult = mysqli_query($conn, $catQuery);
$catRow = mysqli_fetch_assoc($catResult);
$totalCategories = $catRow['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CSE Repository</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Dashboard specific styles */
        .dashboard-header {
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            color: var(--white);
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 300px; height: 300px;
            background: var(--amber);
            opacity: 0.07;
            border-radius: 50%;
        }

        .dashboard-greeting {
            font-family: 'Syne', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .dashboard-greeting span {
            color: var(--amber);
        }

        .dashboard-subtext {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .dashboard-stats {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 1.5rem 2rem;
        }

        .stats-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            text-align: center;
        }

        .stat-card .number {
            font-family: 'Syne', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 0.5rem;
        }

        .stat-card .number span {
            color: var(--amber);
            font-size: 1.8rem;
        }

        .stat-card .label {
            font-size: 0.85rem;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }

        .profile-section {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .profile-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            padding: 1.5rem 2rem;
            color: var(--white);
        }

        .profile-header h2 {
            font-family: 'Syne', sans-serif;
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .profile-body {
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .profile-info-item {
            border-bottom: 1px solid var(--border);
            padding-bottom: 1rem;
        }

        .profile-info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .profile-label {
            font-size: 0.8rem;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        .profile-value {
            font-size: 1rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .dashboard-actions {
            padding: 2rem;
            border-top: 1px solid var(--border);
            background: var(--off-white);
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .projects-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .section-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 1.5rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 40px;
            height: 4px;
            background: var(--amber);
            border-radius: 2px;
            margin-top: 6px;
        }

        .empty-message {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--text-light);
        }

        .empty-message-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .empty-message h3 {
            color: var(--text-mid);
            font-family: 'Syne', sans-serif;
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
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
        <a href="upload.php" class="btn-upload">+ Upload</a>
        <span style="color: rgba(255,255,255,0.5);">|</span>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="logout.php" style="color: #ef4444;">Logout</a>
    </div>
</nav>

<!-- ======================== DASHBOARD HEADER ======================== -->
<section class="dashboard-header">
    <div class="dashboard-greeting">
        👋 Welcome, <span><?php echo htmlspecialchars($user['name']); ?></span>!
    </div>
    <p class="dashboard-subtext">Here's your project dashboard</p>
</section>

<!-- ======================== STATS BAR ======================== -->
<div class="dashboard-stats">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="number"><?php echo $totalProjects; ?><span>+</span></div>
            <div class="label">Your Projects</div>
        </div>
        <div class="stat-card">
            <div class="number"><?php echo $totalCategories; ?><span>+</span></div>
            <div class="label">Categories</div>
        </div>
        <div class="stat-card">
            <div class="number">Sem <span><?php echo $user['semester']; ?></span></div>
            <div class="label">Semester</div>
        </div>
        <div class="stat-card">
            <div class="number">Div <span><?php echo $user['division']; ?></span></div>
            <div class="label">Division</div>
        </div>
    </div>
</div>

<!-- ======================== PROFILE SECTION ======================== -->
<div class="profile-section">
    <div class="profile-card">
        <div class="profile-header">
            <h2>👤 Student Profile</h2>
        </div>
        <div class="profile-body">
            <div class="profile-info-item">
                <div class="profile-label">Full Name</div>
                <div class="profile-value"><?php echo htmlspecialchars($user['name']); ?></div>
            </div>
            <div class="profile-info-item">
                <div class="profile-label">USN</div>
                <div class="profile-value"><?php echo htmlspecialchars($user['usn']); ?></div>
            </div>
            <div class="profile-info-item">
                <div class="profile-label">Email</div>
                <div class="profile-value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            <div class="profile-info-item">
                <div class="profile-label">Phone</div>
                <div class="profile-value"><?php echo htmlspecialchars($user['phone']); ?></div>
            </div>
            <div class="profile-info-item">
                <div class="profile-label">Semester</div>
                <div class="profile-value"><?php echo $user['semester']; ?></div>
            </div>
            <div class="profile-info-item">
                <div class="profile-label">Division</div>
                <div class="profile-value"><?php echo htmlspecialchars($user['division']); ?></div>
            </div>
        </div>
        <div class="dashboard-actions">
            <a href="upload.php" class="btn btn-primary">📤 Upload New Project</a>
            <a href="projects.php" class="btn btn-secondary">📂 View All Projects</a>
        </div>
    </div>
</div>

<!-- ======================== YOUR PROJECTS SECTION ======================== -->
<div class="projects-section">
    <h2 class="section-title">📚 Your Projects</h2>

    <?php if ($totalProjects > 0): ?>
        <div class="projects-grid">
            <?php while ($project = mysqli_fetch_assoc($projectResult)): ?>
                <!-- Each project card -->
                <div class="project-card">
                    
                    <!-- Card header -->
                    <div class="card-header">
                        <span class="card-category"><?php echo htmlspecialchars($project['category']); ?></span>
                        <span class="card-year">📅 <?php echo date('M Y', strtotime($project['created_at'])); ?></span>
                    </div>

                    <!-- Card content -->
                    <div class="card-body">
                        <h3 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h3>
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
                            <input type="hidden" name="redirect" value="dashboard.php">
                            <button type="submit" class="btn btn-delete btn-sm">🗑️ Delete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    <?php else: ?>
        <!-- No projects yet -->
        <div class="empty-message">
            <div class="empty-message-icon">📂</div>
            <h3>No Projects Yet</h3>
            <p>You haven't uploaded any projects yet. Start by creating one!</p>
            <br>
            <a href="upload.php" class="btn btn-primary">📤 Upload Your First Project</a>
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
