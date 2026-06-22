// =========================================================================
// js/app.js — Core Client-Side Logic & Data Management
// This file manages the project data in localStorage and handles
// page-specific features like displaying, searching, and uploading projects.
// =========================================================================

// --- 1. DEFAULT PROJECTS DATABASE ---
// If the user visits the site for the first time, we pre-populate localStorage
// with these 6 sample Computer Science projects (same as the database.sql).
const DEFAULT_PROJECTS = [
    {
        id: 1,
        title: "Smart Attendance System",
        student_name: "Arjun Sharma",
        year: 2024,
        technology: "Python, OpenCV, MySQL",
        category: "AI",
        description: "A face-recognition based attendance system that automatically marks attendance by identifying students through a webcam. Uses OpenCV for face detection and MySQL to store attendance records.",
        github_link: "https://github.com/example/smart-attendance",
        created_at: "2024-03-15T10:00:00.000Z"
    },
    {
        id: 2,
        title: "College Event Portal",
        student_name: "Priya Nair",
        year: 2024,
        technology: "HTML, CSS, PHP, MySQL",
        category: "Web",
        description: "A web portal where students can view upcoming college events, register for them, and get email reminders. Admin panel allows staff to post and manage events easily.",
        github_link: "https://github.com/example/event-portal",
        created_at: "2024-04-10T12:00:00.000Z"
    },
    {
        id: 3,
        title: "Smart Plant Watering System",
        student_name: "Rahul Verma",
        year: 2023,
        technology: "Arduino, C++, IoT Sensors",
        category: "IoT",
        description: "An automated plant watering system using Arduino and soil moisture sensors. The system checks soil moisture every hour and waters the plant automatically when needed. Data is logged to a web dashboard.",
        github_link: "https://github.com/example/smart-plant",
        created_at: "2023-08-20T14:30:00.000Z"
    },
    {
        id: 4,
        title: "Student Grade Calculator",
        student_name: "Sneha Patel",
        year: 2023,
        technology: "HTML, CSS, JavaScript",
        category: "Web",
        description: "A simple web application where students enter their marks and instantly see their CGPA, percentage, and grade. Supports different grading systems and can export results as PDF.",
        github_link: "https://github.com/example/grade-calc",
        created_at: "2023-09-05T09:15:00.000Z"
    },
    {
        id: 5,
        title: "Fake News Detector",
        student_name: "Karan Mehta",
        year: 2024,
        technology: "Python, Machine Learning, Flask",
        category: "AI",
        description: "A machine learning model trained on a dataset of real and fake news articles. The web interface allows users to paste a news article and instantly get a prediction on whether it is real or fake.",
        github_link: "https://github.com/example/fake-news-detector",
        created_at: "2024-05-01T16:45:00.000Z"
    },
    {
        id: 6,
        title: "Home Automation with Raspberry Pi",
        student_name: "Divya Krishnan",
        year: 2023,
        technology: "Raspberry Pi, Python, MQTT",
        category: "IoT",
        description: "Control home appliances (lights, fan, AC) remotely using a mobile-friendly web interface. Uses Raspberry Pi as the central controller and MQTT protocol for communication between devices.",
        github_link: "https://github.com/example/home-automation",
        created_at: "2023-11-12T11:20:00.000Z"
    }
];

// --- 2. LOCALSTORAGE HELPER FUNCTIONS ---

// Retrieve the list of all projects from localStorage
function getProjects() {
    let projects = localStorage.getItem("cse_projects");
    
    // If no projects in localStorage, save the defaults and return them
    if (!projects) {
        localStorage.setItem("cse_projects", JSON.stringify(DEFAULT_PROJECTS));
        return DEFAULT_PROJECTS;
    }
    
    // Parse the JSON string back into a JavaScript array
    return JSON.parse(projects);
}

// Save a new project into localStorage
function saveProject(newProject) {
    let projects = getProjects();
    
    // Generate a unique ID (highest existing ID + 1)
    let maxId = 0;
    projects.forEach(p => {
        if (p.id > maxId) maxId = p.id;
    });
    newProject.id = maxId + 1;
    newProject.created_at = new Date().toISOString();
    
    // Add the new project to the array
    projects.push(newProject);
    
    // Save the updated array back to localStorage as a string
    localStorage.setItem("cse_projects", JSON.stringify(projects));
    return newProject.id;
}

// --- 3. DYNAMIC RENDERING HELPERS ---

// Helper function to create HTML for a project card
function createProjectCardHTML(project) {
    // Map categories to style classes
    const catClass = "badge-" + project.category.toLowerCase();
    
    return `
        <a class="project-card" href="project.html?id=${project.id}">
            <div class="card-top">
                <span class="badge ${catClass}">${escapeHTML(project.category)}</span>
                <span class="card-year">${project.year}</span>
            </div>
            <div class="card-title">${escapeHTML(project.title)}</div>
            <div class="card-student">by ${escapeHTML(project.student_name)}</div>
            <span class="card-tech">${escapeHTML(project.technology)}</span>
        </a>
    `;
}

// Helper to escape HTML and prevent security issues (XSS)
function escapeHTML(str) {
    if (!str) return "";
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// --- 4. PAGE INITIALIZATION SCRIPTS ---

// Run this when the DOM content is fully loaded
document.addEventListener("DOMContentLoaded", () => {
    
    // Determine which page we are on by checking for unique element IDs
    
    // ----------------------------------------------------
    // PAGE A: HOME PAGE (index.html)
    // ----------------------------------------------------
    if (document.getElementById("home-page")) {
        const projects = getProjects();
        
        // Update stats counters
        const totalProjectsEl = document.getElementById("stat-total-projects");
        const totalYearsEl = document.getElementById("stat-total-years");
        
        if (totalProjectsEl) {
            totalProjectsEl.textContent = projects.length;
        }
        
        if (totalYearsEl) {
            // Count unique years using a Set
            const yearsSet = new Set(projects.map(p => p.year));
            totalYearsEl.textContent = yearsSet.size;
        }
        
        // Display 6 most recent projects (sorted by created_at or id descending)
        const recentGrid = document.getElementById("recent-projects-grid");
        if (recentGrid) {
            // Sort: newest projects first (higher ID or newer date)
            const sortedProjects = [...projects].sort((a, b) => b.id - a.id);
            
            // Slice the first 6 projects
            const recent6 = sortedProjects.slice(0, 6);
            
            if (recent6.length === 0) {
                recentGrid.innerHTML = `<p style="grid-column: 1/-1; color: var(--text-light);">No projects found.</p>`;
            } else {
                recentGrid.innerHTML = recent6.map(p => createProjectCardHTML(p)).join("");
            }
        }
    }
    
    // ----------------------------------------------------
    // PAGE B: ALL PROJECTS PAGE (projects.html)
    // ----------------------------------------------------
    if (document.getElementById("projects-page")) {
        const projects = getProjects();
        
        const searchInput = document.getElementById("search-input");
        const categoryFilter = document.getElementById("category-filter");
        const projectsGrid = document.getElementById("projects-grid");
        const resultCount = document.getElementById("result-count");
        const noResults = document.getElementById("no-results");
        
        // Check URL parameters for search and category filters (e.g. projects.html?search=attendance or projects.html?category=Web)
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get("search");
        const categoryParam = urlParams.get("category");
        
        if (searchParam && searchInput) {
            searchInput.value = searchParam;
        }
        if (categoryParam && categoryFilter) {
            // Match category case-insensitively or set directly if matching
            categoryFilter.value = categoryParam.toLowerCase();
        }
        
        // Render and filter cards function
        function filterAndRender() {
            const searchText = searchInput ? searchInput.value.toLowerCase().trim() : "";
            const selectedCategory = categoryFilter ? categoryFilter.value.toLowerCase() : "all";
            
            // Sort: newest first
            const sortedProjects = [...projects].sort((a, b) => b.id - a.id);
            
            // Filter the projects array
            const filtered = sortedProjects.filter(p => {
                const matchesSearch = 
                    p.title.toLowerCase().includes(searchText) ||
                    p.technology.toLowerCase().includes(searchText) ||
                    p.student_name.toLowerCase().includes(searchText);
                
                const matchesCategory = 
                    selectedCategory === "all" || 
                    p.category.toLowerCase() === selectedCategory;
                
                return matchesSearch && matchesCategory;
            });
            
            // Render the filtered projects
            if (projectsGrid) {
                if (filtered.length === 0) {
                    projectsGrid.innerHTML = "";
                    if (noResults) noResults.style.display = "block";
                } else {
                    projectsGrid.innerHTML = filtered.map(p => createProjectCardHTML(p)).join("");
                    if (noResults) noResults.style.display = "none";
                }
            }
            
            // Update result count
            if (resultCount) {
                resultCount.textContent = `Showing ${filtered.length} project${filtered.length !== 1 ? 's' : ''}`;
            }
        }
        
        // Attach event listeners for real-time live filtering
        if (searchInput) {
            searchInput.addEventListener("input", filterAndRender);
        }
        if (categoryFilter) {
            categoryFilter.addEventListener("change", filterAndRender);
        }
        
        // Perform initial render
        filterAndRender();
    }
    
    // ----------------------------------------------------
    // PAGE C: PROJECT DETAIL PAGE (project.html)
    // ----------------------------------------------------
    if (document.getElementById("detail-page")) {
        const urlParams = new URLSearchParams(window.location.search);
        const projectId = parseInt(urlParams.get("id"));
        
        // If ID is missing or invalid, go back to projects list
        if (!projectId || isNaN(projectId)) {
            window.location.href = "projects.html";
            return;
        }
        
        const projects = getProjects();
        const project = projects.find(p => p.id === projectId);
        
        // If project not found, go back to projects list
        if (!project) {
            window.location.href = "projects.html";
            return;
        }
        
        // Populate the details page DOM elements
        document.title = `${project.title} — CSE Repository`;
        
        const detailCard = document.getElementById("project-detail-card");
        if (detailCard) {
            const catClass = "badge-" + project.category.toLowerCase();
            
            // Build GitHub section HTML
            let githubHTML = `<p style="color: var(--text-light); font-size:14px;">No GitHub link provided for this project.</p>`;
            if (project.github_link && project.github_link.trim() !== "") {
                githubHTML = `
                    <a class="btn-github" href="${escapeHTML(project.github_link)}" target="_blank">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/>
                        </svg>
                        View on GitHub
                    </a>
                `;
            }
            
            // Format description: replace newlines with HTML line breaks
            const formattedDescription = escapeHTML(project.description).replace(/\n/g, "<br>");
            
            detailCard.innerHTML = `
                <!-- Category badge -->
                <span class="badge ${catClass}" style="display:inline-block; margin-bottom:12px;">
                    ${escapeHTML(project.category)}
                </span>

                <!-- Project title -->
                <h1>${escapeHTML(project.title)}</h1>

                <!-- Meta info row -->
                <div class="detail-meta">
                    <div class="meta-item">
                        👤 <strong>Student:</strong> ${escapeHTML(project.student_name)}
                    </div>
                    <div class="meta-item">
                        📅 <strong>Year:</strong> ${project.year}
                    </div>
                    <div class="meta-item">
                        🛠️ <strong>Technologies:</strong> ${escapeHTML(project.technology)}
                    </div>
                </div>

                <!-- Full description -->
                <h3 style="margin-bottom:10px;">About This Project</h3>
                <p class="detail-description">${formattedDescription}</p>

                <!-- GitHub link -->
                ${githubHTML}
            `;
        }
    }
    
    // ----------------------------------------------------
    // PAGE D: UPLOAD PAGE (upload.html)
    // ----------------------------------------------------
    if (document.getElementById("upload-page")) {
        const uploadForm = document.getElementById("project-upload-form");
        const alertBox = document.getElementById("alert-box");
        
        if (uploadForm) {
            uploadForm.addEventListener("submit", (event) => {
                // Prevent the form from trying to submit to a server (which would reload the page)
                event.preventDefault();
                
                // Read input values
                const title = document.getElementById("title").value.trim();
                const studentName = document.getElementById("student_name").value.trim();
                const year = parseInt(document.getElementById("year").value.trim());
                const category = document.getElementById("category").value.trim();
                const technology = document.getElementById("technology").value.trim();
                const description = document.getElementById("description").value.trim();
                const githubLink = document.getElementById("github_link").value.trim();
                
                // Reset alert box
                if (alertBox) {
                    alertBox.className = "alert";
                    alertBox.style.display = "none";
                    alertBox.innerHTML = "";
                }
                
                // Simple validation
                if (!title || !studentName || !year || !category || !technology || !description) {
                    showAlert("Please fill in all required fields.", "error");
                    return;
                }
                
                if (isNaN(year) || year < 2000 || year > 2099) {
                    showAlert("Please enter a valid year (between 2000 and 2099).", "error");
                    return;
                }
                
                // Construct the project object
                const newProject = {
                    title: title,
                    student_name: studentName,
                    year: year,
                    category: category,
                    technology: technology,
                    description: description,
                    github_link: githubLink
                };
                
                // Save project using our localStorage helper function
                const newId = saveProject(newProject);
                
                // Show success message
                showAlert(`Project uploaded successfully! <a href="project.html?id=${newId}">View your project →</a>`, "success");
                
                // Reset form fields
                uploadForm.reset();
            });
        }
        
        // Helper function to display alert boxes
        function showAlert(message, type) {
            if (alertBox) {
                alertBox.innerHTML = message;
                alertBox.className = `alert alert-${type}`;
                alertBox.style.display = "block";
                
                // Scroll to top of the page so the user sees the alert
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    }
});
