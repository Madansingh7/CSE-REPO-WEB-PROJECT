# CSE Project Repository
### A Simple, Beginner-Friendly HTML, CSS, and JavaScript Web Application for Computer Science Students

This project has been completely rebuilt to use only static frontend technologies (**HTML5**, **CSS3**, and **Vanilla JavaScript**). It is designed to be extremely lightweight, easy to understand for beginners, and runs directly in the browser without requiring any local servers, databases, or backend installation.

---

## Folder Structure

All files are located in the root folder for simplicity:

```
webtech-project/
│
├── index.html       ← Home page (Hero section, dynamic stats, categories, recent projects grid)
├── projects.html    ← Projects listing page with real-time search and category filter
├── project.html     ← Detailed view of a single project (reads project ID from URL parameters)
├── upload.html      ← Form to submit/upload a new project
├── style.css        ← Sleek, modern, responsive CSS styling for all pages
├── app.js           ← Client-side database using localStorage (manages project data, upload form, search/filter)
└── README.md        ← This documentation file
```

---

## How It Works (No Installation Needed!)

Since this project runs entirely on the frontend, you do not need XAMPP, Apache, or MySQL.

### Step 1: Open the Project
Simply double-click **`index.html`** to open the site in your favorite web browser (Chrome, Firefox, Safari, Edge).

### Step 2: Use the Website
- **Home Page (`index.html`)**: Check out statistics, click category pills, or search for projects.
- **Search & Filters (`projects.html`)**: Type in the search box to filter projects instantly by title, technologies used, or student name without reloading the page.
- **Detail View (`project.html`)**: Click any project card to view its full details, including student name, technologies, description, and an optional link to its GitHub repository.
- **Upload (`upload.html`)**: Submit new projects using the form. The project is saved directly into your browser's local database (`localStorage`) so that it persists and shows up under **All Projects** and **Recent Projects**!

---

## How Data is Managed (`localStorage`)

- **Default Data**: On the first load, the application automatically populates itself with 6 default student projects.
- **Persistence**: Any project uploaded through the "Upload Project" form will be stored in your browser's `localStorage` and will persist even if you refresh or close the tab.
- **Data Schema**:
  ```json
  {
      "id": 1,
      "title": "Project Title",
      "student_name": "Student's Name",
      "year": 2024,
      "technology": "HTML, CSS, JavaScript",
      "category": "Web",
      "description": "Full description of the project...",
      "github_link": "https://github.com/..."
  }
  ```

---

## Key Features

1. **Vanilla JavaScript**: Uses standard, modern, beginner-friendly JavaScript without any complex frameworks.
2. **Responsive Design**: Mobile-friendly layout using media queries in CSS.
3. **No Database Configuration**: Instant deployment on platforms like GitHub Pages, Vercel, or Netlify.
4. **Interactive Filters**: Instant searching and category filtering on the frontend.
