// ============================================================
//  script.js - CSE Project Repository JavaScript
//  Handles: Form Validation, Search/Filter, Confirmation dialogs
// ============================================================

// ============================================================
//  1. FORM VALIDATION - Upload & Edit Project
// ============================================================

// This function validates the project upload/edit form
// Returns true if all fields are valid, false if any error found
function validateProjectForm() {

    // Clear all previous error messages first
    clearAllErrors();

    let isValid = true; // We assume valid until an error is found

    // --- Validate Title ---
    const title = document.getElementById('title');
    if (title) {
        if (title.value.trim() === '') {
            showError('titleError', 'Project title is required.');
            markError(title);
            isValid = false;
        } else if (title.value.trim().length < 5) {
            showError('titleError', 'Title must be at least 5 characters long.');
            markError(title);
            isValid = false;
        } else if (title.value.trim().length > 255) {
            showError('titleError', 'Title cannot exceed 255 characters.');
            markError(title);
            isValid = false;
        }
    }

    // --- Validate Student Name ---
    const studentName = document.getElementById('student_name');
    if (studentName) {
        if (studentName.value.trim() === '') {
            showError('studentNameError', 'Student name is required.');
            markError(studentName);
            isValid = false;
        } else if (studentName.value.trim().length < 3) {
            showError('studentNameError', 'Name must be at least 3 characters.');
            markError(studentName);
            isValid = false;
        } else if (!/^[a-zA-Z\s]+$/.test(studentName.value.trim())) {
            // Only letters and spaces allowed in a name
            showError('studentNameError', 'Name should contain only letters and spaces.');
            markError(studentName);
            isValid = false;
        }
    }

    // --- Validate Year ---
    const year = document.getElementById('year');
    if (year) {
        const yearValue = parseInt(year.value);
        const currentYear = new Date().getFullYear();

        if (year.value.trim() === '') {
            showError('yearError', 'Year is required.');
            markError(year);
            isValid = false;
        } else if (isNaN(yearValue)) {
            showError('yearError', 'Please enter a valid year number.');
            markError(year);
            isValid = false;
        } else if (yearValue < 2000 || yearValue > currentYear) {
            // Year must be between 2000 and current year
            showError('yearError', 'Year must be between 2000 and ' + currentYear + '.');
            markError(year);
            isValid = false;
        }
    }

    // --- Validate Category ---
    const category = document.getElementById('category');
    if (category) {
        if (category.value === '' || category.value === 'Select Category') {
            showError('categoryError', 'Please select a project category.');
            markError(category);
            isValid = false;
        }
    }

    // --- Validate Technology ---
    const technology = document.getElementById('technology');
    if (technology) {
        if (technology.value.trim() === '') {
            showError('technologyError', 'Technology/tools used is required.');
            markError(technology);
            isValid = false;
        } else if (technology.value.trim().length < 2) {
            showError('technologyError', 'Please provide at least one technology.');
            markError(technology);
            isValid = false;
        }
    }

    // --- Validate Description ---
    const description = document.getElementById('description');
    if (description) {
        if (description.value.trim() === '') {
            showError('descriptionError', 'Project description is required.');
            markError(description);
            isValid = false;
        } else if (description.value.trim().length < 20) {
            showError('descriptionError', 'Description must be at least 20 characters.');
            markError(description);
            isValid = false;
        } else if (description.value.trim().length > 2000) {
            showError('descriptionError', 'Description cannot exceed 2000 characters.');
            markError(description);
            isValid = false;
        }
    }

    // --- Validate GitHub Link (optional but format must be correct if provided) ---
    const githubLink = document.getElementById('github_link');
    if (githubLink && githubLink.value.trim() !== '') {
        // If user typed something, check if it's a valid GitHub URL
        const githubPattern = /^https?:\/\/(www\.)?github\.com\/.+/i;
        if (!githubPattern.test(githubLink.value.trim())) {
            showError('githubError', 'Please enter a valid GitHub URL. Example: https://github.com/username/repo');
            markError(githubLink);
            isValid = false;
        }
    }

    // If there are errors, scroll to the first error so user can see it
    if (!isValid) {
        const firstError = document.querySelector('.error-input');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
    }

    return isValid; // true = submit form, false = stop submission
}


// ============================================================
//  HELPER FUNCTIONS FOR VALIDATION
// ============================================================

// Show an error message under a field
function showError(elementId, message) {
    const errorDiv = document.getElementById(elementId);
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }
}

// Add red border to an invalid input field
function markError(inputElement) {
    inputElement.classList.add('error-input');
}

// Clear all error messages and red borders
function clearAllErrors() {
    // Remove all red borders
    document.querySelectorAll('.error-input').forEach(function(el) {
        el.classList.remove('error-input');
    });
    // Hide all error messages
    document.querySelectorAll('.field-error').forEach(function(el) {
        el.style.display = 'none';
        el.textContent = '';
    });
}


// ============================================================
//  2. LIVE FIELD VALIDATION (clears error when user fixes it)
// ============================================================

// Add event listeners to form fields once page loads
document.addEventListener('DOMContentLoaded', function() {

    // For each input field, clear its error when user starts typing
    const fields = ['title', 'student_name', 'year', 'category', 'technology', 'description', 'github_link'];
    
    fields.forEach(function(fieldId) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                this.classList.remove('error-input');
                // Try to find associated error div and hide it
                const errorId = fieldId.replace('_', '') + 'Error';
                const customErrorIds = {
                    'title': 'titleError',
                    'student_name': 'studentNameError',
                    'year': 'yearError',
                    'category': 'categoryError',
                    'technology': 'technologyError',
                    'description': 'descriptionError',
                    'github_link': 'githubError'
                };
                const errorDiv = document.getElementById(customErrorIds[fieldId]);
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            });

            // Same for select (change event instead of input)
            field.addEventListener('change', function() {
                this.classList.remove('error-input');
                const customErrorIds = {
                    'category': 'categoryError'
                };
                if (customErrorIds[fieldId]) {
                    const errorDiv = document.getElementById(customErrorIds[fieldId]);
                    if (errorDiv) errorDiv.style.display = 'none';
                }
            });
        }
    });


    // ============================================================
    //  3. CHARACTER COUNT for Description field
    // ============================================================
    const descField = document.getElementById('description');
    const charCounter = document.getElementById('charCount');
    
    if (descField && charCounter) {
        // Show count on page load (if editing existing project)
        charCounter.textContent = descField.value.length + ' / 2000';
        
        descField.addEventListener('input', function() {
            const count = this.value.length;
            charCounter.textContent = count + ' / 2000';
            
            // Turn counter red when close to limit
            if (count > 1800) {
                charCounter.style.color = '#ef4444';
            } else {
                charCounter.style.color = '';
            }
        });
    }


    // ============================================================
    //  4. SEARCH AND FILTER (on projects.php page)
    // ============================================================
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const projectCards = document.querySelectorAll('.project-card');
    const resultsCount = document.getElementById('resultsCount');

    // Function to filter project cards based on search and category
    function filterProjects() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const selectedCategory = categoryFilter ? categoryFilter.value.toLowerCase() : '';
        
        let visibleCount = 0;

        projectCards.forEach(function(card) {
            // Get text content from the card to search through
            const title = card.querySelector('.card-title') ? card.querySelector('.card-title').textContent.toLowerCase() : '';
            const student = card.querySelector('.card-student') ? card.querySelector('.card-student').textContent.toLowerCase() : '';
            const desc = card.querySelector('.card-desc') ? card.querySelector('.card-desc').textContent.toLowerCase() : '';
            const tech = card.querySelector('.card-tech') ? card.querySelector('.card-tech').textContent.toLowerCase() : '';
            const category = card.getAttribute('data-category') ? card.getAttribute('data-category').toLowerCase() : '';

            // Check if search term matches any field
            const matchesSearch = searchTerm === '' || 
                title.includes(searchTerm) || 
                student.includes(searchTerm) || 
                desc.includes(searchTerm) || 
                tech.includes(searchTerm);

            // Check if category matches the filter
            const matchesCategory = selectedCategory === '' || category.includes(selectedCategory);

            // Show or hide the card
            if (matchesSearch && matchesCategory) {
                card.style.display = 'flex';
                card.style.flexDirection = 'column';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Update results count text
        if (resultsCount) {
            resultsCount.textContent = visibleCount + ' project(s) found';
        }

        // Show empty state if nothing matches
        const emptyState = document.getElementById('emptyState');
        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    // Attach filter function to search box
    if (searchInput) {
        searchInput.addEventListener('input', filterProjects);
    }

    // Attach filter function to category dropdown
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterProjects);
    }


    // ============================================================
    //  5. AUTO-HIDE SUCCESS/ERROR ALERTS after 5 seconds
    // ============================================================
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500);
        }, 5000); // 5000ms = 5 seconds
    });

}); // End of DOMContentLoaded


// ============================================================
//  6. DELETE CONFIRMATION
// ============================================================

// This function is called when Delete button is clicked
// Returns true to allow form submit, false to cancel
function confirmDelete(projectTitle) {
    return confirm(
        '⚠️ DELETE PROJECT\n\n' +
        'Are you sure you want to delete:\n"' + projectTitle + '"?\n\n' +
        'This action CANNOT be undone!'
    );
}


// ============================================================
//  7. YEAR INPUT - only allow numbers
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    const yearField = document.getElementById('year');
    if (yearField) {
        yearField.addEventListener('keypress', function(e) {
            // Only allow digit keys (0-9)
            if (e.key && isNaN(parseInt(e.key))) {
                e.preventDefault();
            }
        });
        
        // Set max year to current year automatically
        yearField.setAttribute('max', new Date().getFullYear());
    }
});
