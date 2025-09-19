<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: intro.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Get user profile picture
$sql = "SELECT profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$profile_pic = $user['profile_pic'];
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>NOTEIt - Your Notes</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f5f5f5;
        }

        .main-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar-container {
            width: 250px;
            background-color: white;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-container h1 {
            margin-bottom: 30px;
            font-size: 24px;
        }

        .sidebar-container h1 span {
            color: #4CAF50;
        }

        .option-1, .option-2, .option-3, .option-4 {
            display: flex;
            align-items: center;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .option-1:hover, .option-2:hover, .option-3:hover, .option-4:hover {
            background-color: rgba(76, 175, 80, 0.1);
        }

        .active-option {
            background-color: rgba(76, 175, 80, 0.1);
            border-left: 3px solid #4CAF50;
        }

        .option-1 i, .option-2 i, .option-3 i, .option-4 i {
            font-size: 20px;
            margin-right: 10px;
        }

        .user-container {
            margin-top: auto;
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 10px;
        }

        .user-container img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }

        .bold-text {
            font-weight: bold;
        }

        .break-text {
            font-weight: normal;
            font-size: 14px;
            color: #666;
        }

        .view-more {
            font-size: 12px;
            color: #4CAF50;
            cursor: pointer;
            display: flex;
            align-items: center;
            margin-top: 5px;
        }

        .view-more i {
            margin-left: 5px;
            transition: transform 0.3s;
        }

        .notes-container {
            flex: 1;
            padding: 20px;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .green-container {
            display: flex;
            flex-direction: column;
        }

        .navbar-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .green-line {
            width: 50px;
            height: 3px;
            background-color: #4CAF50;
        }

        .flex-container {
            display: flex;
            align-items: center;
        }

        .searchbar {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            margin-right: 20px;
            width: 250px;
        }

        .add-container {
            display: flex;
            align-items: center;
            background-color: #4CAF50;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .add-icon {
            width: 20px;
            height: 20px;
            margin-right: 5px;
        }

        .notes {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .boxes {
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .notes-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .notes-number {
            font-weight: bold;
        }

        .sample-notes {
            margin-bottom: 15px;
        }

        .sample-note {
            margin-bottom: 5px;
            color: #555;
        }

        .footer-note {
            display: flex;
            align-items: center;
        }

        .circle {
            width: 10px;
            height: 10px;
            background-color: #4CAF50;
            border-radius: 50%;
            margin-right: 10px;
        }

        .circle.favorite {
            background-color: #FFD700;
        }

        .date {
            font-size: 12px;
            color: #888;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 500px;
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
        }

        .note-input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .note-textarea {
            width: 100%;
            height: 150px;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
        }

        #save-note {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .note-options-menu {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            position: absolute;
            z-index: 1001;
        }

        .menu-option {
            padding: 8px 12px;
            cursor: pointer;
        }

        .menu-option:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <div class="sidebar-container">
            <h1>NOTE<span>It!</span></h1>
            <div class="option-1 active-option" data-view="all">
                <i class='bx bx-calendar-plus'></i>
                <p class="option1-name">
                    All Notes
                </p>
            </div>
            <div class="option-2" data-view="favorites">
                <i class='bx bx-heart'></i>
                <p class="option2-name">
                    Favorites
                </p>
            </div>
            <div class="option-3" data-view="archives">
                <i class='bx bx-box'></i>
                <p class="option3-name">
                    Archives
                </p>
            </div>
            <div class="option-4">
                <i class='bx bx-power-off'></i>
                <p class="option4-name">
                    Logout
                </p>
            </div>
            <div class="user-container">
                <img src="<?php echo file_exists('uploads/' . $profile_pic) ? 'uploads/' . $profile_pic : 'https://yt3.googleusercontent.com/8eGqQZxpSdVKFel6OBsW5orqJ1mC_2h_sPKbR4al6eqOM2rV-2ahi6FhEWT-L9PmWjgfhsJhSeY=s900-c-k-c0x00ffffff-no-rj'; ?>">
                <div class="names">
                    <p class="bold-text">Hi <?php echo htmlspecialchars($name); ?>!<br><span class="break-text">Welcome Back.</span></p>
                    <div class="view-more">
                        View More
                        <i class='bx bx-down-arrow-alt' id="drop-down"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="notes-container">
            <header class="navbar">
                <div class="green-container">
                    <p class="navbar-name" id="view-title">All Notes</p>
                    <div class="green-line"></div>
                </div>
                <div class="flex-container">
                    <input type="search" name="search" class="searchbar" placeholder="Search" id="search-input">
                    <div class="add-container" id="add-note-btn">
                        <img src="assets/icons8-add-50.png" class="add-icon" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2220%22 height=%2220%22 viewBox=%220 0 24 24%22 fill=%22white%22><path d=%22M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z%22/></svg>'">
                        <p>Add Notes</p>
                    </div>
                </div>
            </header>
            <div class="notes" id="notes-container">
                <!-- Notes will be loaded here via AJAX -->
            </div>
        </div>
    </div>

    <!-- Add Note Modal -->
    <div class="modal" id="add-note-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Add New Note</h2>
            <input type="text" id="note-title" placeholder="Title" class="note-input">
            <textarea id="note-content" placeholder="Your note..." class="note-textarea"></textarea>
            <button id="save-note">Save Note</button>
        </div>
    </div>

    <!-- Note Options Menu (will be created dynamically) -->
    <div class="note-options-menu" id="note-options-menu" style="display: none;">
        <div class="menu-option favorite-option" id="favorite-option">Add to Favorites</div>
        <div class="menu-option archive-option" id="archive-option">Archive</div>
        <div class="menu-option delete-option" id="delete-option">Delete</div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const allNotesBtn = document.querySelector('.option-1');
            const favoritesBtn = document.querySelector('.option-2');
            const archivesBtn = document.querySelector('.option-3');
            const logoutBtn = document.querySelector('.option-4');
            const searchInput = document.getElementById('search-input');
            const addNoteBtn = document.getElementById('add-note-btn');
            const notesContainer = document.getElementById('notes-container');
            const viewTitle = document.getElementById('view-title');
            const viewMoreBtn = document.querySelector('.view-more');
            const dropDownIcon = document.getElementById('drop-down');
            
            // Modal elements
            const addNoteModal = document.getElementById('add-note-modal');
            const closeModal = document.querySelector('.close-modal');
            const saveNoteBtn = document.getElementById('save-note');
            const noteTitleInput = document.getElementById('note-title');
            const noteContentInput = document.getElementById('note-content');
            
            // Note options menu
            const noteOptionsMenu = document.getElementById('note-options-menu');
            const favoriteOption = document.getElementById('favorite-option');
            const archiveOption = document.getElementById('archive-option');
            const deleteOption = document.getElementById('delete-option');
            
            // Current view and active note
            let currentView = 'all';
            let activeNoteId = null;
            
            // Load notes based on current view
            function loadNotes(view = currentView, searchQuery = '') {
                fetch(`get_notes.php?view=${view}&search=${encodeURIComponent(searchQuery)}`)
                    .then(response => response.json())
                    .then(data => {
                        notesContainer.innerHTML = '';
                        
                        if (data.length === 0) {
                            notesContainer.innerHTML = '<p>No notes found.</p>';
                            return;
                        }
                        
                        data.forEach(note => {
                            const noteBox = document.createElement('div');
                            noteBox.className = 'boxes';
                            
                            // Parse the content JSON string to array
                            let contentArray;
                            try {
                                contentArray = JSON.parse(note.content);
                            } catch (e) {
                                contentArray = [note.content];
                            }
                            
                            // Format the date
                            const date = new Date(note.date_created);
                            const formattedDate = date.toLocaleDateString('en-US', {
                                month: 'long',
                                day: '2-digit',
                                year: 'numeric'
                            });
                            
                            noteBox.innerHTML = `
                                <div class="notes-header">
                                    <p class="notes-number">${note.title}</p>
                                    <i class='bx bx-dots-horizontal-rounded' data-id="${note.id}"></i>
                                </div>
                                <div class="sample-notes">
                                    ${contentArray.map(line => `<p class="sample-note">${line}</p>`).join('')}
                                </div>
                                <div class="footer-note">
                                    <div class="circle ${note.is_favorite === '1' ? 'favorite' : ''}"></div>
                                    <p class="date">${formattedDate}</p>
                                </div>
                            `;
                            
                            // Add options menu event listener
                            const optionsBtn = noteBox.querySelector('.bx-dots-horizontal-rounded');
                            optionsBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                showOptionsMenu(note.id, note.is_favorite === '1', note.is_archived === '1', e);
                            });
                            
                            notesContainer.appendChild(noteBox);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading notes:', error);
                        notesContainer.innerHTML = '<p>Error loading notes. Please try again.</p>';
                    });
            }
            
            // Show options menu for a note
            function showOptionsMenu(noteId, isFavorite, isArchived, event) {
                activeNoteId = noteId;
                
                // Update menu options based on current state
                favoriteOption.textContent = isFavorite ? 'Remove from Favorites' : 'Add to Favorites';
                archiveOption.textContent = isArchived ? 'Unarchive' : 'Archive';
                
                // Position and show menu
                const rect = event.target.getBoundingClientRect();
                noteOptionsMenu.style.top = `${rect.bottom + window.scrollY}px`;
                noteOptionsMenu.style.left = `${rect.left + window.scrollX - 100}px`;
                noteOptionsMenu.style.display = 'block';
                
                // Close menu when clicking elsewhere
                document.addEventListener('click', closeOptionsMenu);
            }
            
            // Close options menu
            function closeOptionsMenu(e) {
                if (!noteOptionsMenu.contains(e.target) && !e.target.classList.contains('bx-dots-horizontal-rounded')) {
                    noteOptionsMenu.style.display = 'none';
                    document.removeEventListener('click', closeOptionsMenu);
                }
            }
            
            // Toggle favorite status
            function toggleFavorite() {
                if (activeNoteId) {
                    fetch('update_note.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=toggle_favorite&note_id=${activeNoteId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadNotes(currentView, searchInput.value);
                        } else {
                            alert('Error updating note');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                    
                    noteOptionsMenu.style.display = 'none';
                }
            }
            
            // Toggle archive status
            function toggleArchive() {
                if (activeNoteId) {
                    fetch('update_note.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=toggle_archive&note_id=${activeNoteId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadNotes(currentView, searchInput.value);
                        } else {
                            alert('Error updating note');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                    
                    noteOptionsMenu.style.display = 'none';
                }
            }
            
            // Delete note
            function deleteNote() {
                if (activeNoteId && confirm('Are you sure you want to delete this note?')) {
                    fetch('update_note.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=delete&note_id=${activeNoteId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadNotes(currentView, searchInput.value);
                        } else {
                            alert('Error deleting note');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                    
                    noteOptionsMenu.style.display = 'none';
                }
            }
            
            // Save new note
            function saveNote() {
                const title = noteTitleInput.value.trim() || 'Untitled Note';
                const content = noteContentInput.value.trim();
                
                if (content) {
                    const contentLines = content.split('\n').filter(line => line.trim());
                    const contentJSON = JSON.stringify(contentLines);
                    
                    fetch('update_note.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=add&title=${encodeURIComponent(title)}&content=${encodeURIComponent(contentJSON)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Clear form and close modal
                            noteTitleInput.value = '';
                            noteContentInput.value = '';
                            addNoteModal.style.display = 'none';
                            
                            // Reload notes
                            loadNotes(currentView, searchInput.value);
                        } else {
                            alert('Error adding note');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                } else {
                    alert('Please enter some content for your note');
                }
            }
            
            // Event listeners
            allNotesBtn.addEventListener('click', function() {
                currentView = 'all';
                viewTitle.textContent = 'All Notes';
                loadNotes('all', searchInput.value);
                setActiveOption(allNotesBtn);
            });
            
            favoritesBtn.addEventListener('click', function() {
                currentView = 'favorites';
                viewTitle.textContent = 'Favorites';
                loadNotes('favorites', searchInput.value);
                setActiveOption(favoritesBtn);
            });
            
            archivesBtn.addEventListener('click', function() {
                currentView = 'archives';
                viewTitle.textContent = 'Archives';
                loadNotes('archives', searchInput.value);
                setActiveOption(archivesBtn);
            });
            
            logoutBtn.addEventListener('click', function() {
                window.location.href = 'logout.php';
            });
            
            searchInput.addEventListener('input', function() {
                loadNotes(currentView, this.value);
            });
            
            addNoteBtn.addEventListener('click', function() {
                addNoteModal.style.display = 'flex';
            });
            
            closeModal.addEventListener('click', function() {
                addNoteModal.style.display = 'none';
            });
            
            // Close modal when clicking outside
            addNoteModal.addEventListener('click', function(e) {
                if (e.target === addNoteModal) {
                    addNoteModal.style.display = 'none';
                }
            });
            
            saveNoteBtn.addEventListener('click', saveNote);
            
            // Note options menu event listeners
            favoriteOption.addEventListener('click', toggleFavorite);
            archiveOption.addEventListener('click', toggleArchive);
            deleteOption.addEventListener('click', deleteNote);
        });
            
            // Set active option
            function setActiveOption(selectedOption) {
                const options = document.querySelectorAll('.sidebar-container div');
                options.forEach(option => {
                    option.classList.remove('active-option');
                });
                selectedOption.classList.add('active-option');
            }

            // Initial load of notes
            loadNotes(currentView);
        
    </script>
</body>

</html>