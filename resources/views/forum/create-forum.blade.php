<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Forum - Material Forum</title>
    <link rel="stylesheet" href="{{ asset('assets/css/forum.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/create-forum.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css') }}">
</head>
<body>
    <header class="create-forum-header">
        <div class="header-left">
            <a href="forum.html" class="logo">
                <i class="fas fa-comments"></i>
                <span>Material Forum</span>
            </a>
            <button class="back-btn" onclick="window.location.href='forum.html'">
                <i class="fas fa-arrow-left"></i>
                Back to Forums
            </button>
        </div>
    </header>

    <div class="create-forum-container">
        <div class="create-forum-card">
            <div class="card-header">
                <h2>Create New Forum</h2>
                <p>Start a new community for discussions and collaboration</p>
            </div>

            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle"></i> Forum created successfully!
            </div>

            <div class="error-message" id="errorMessage"></div>

            <form id="createForumForm">
                <div class="form-section">
                    <div class="form-section-title">Basic Information</div>
                    
                    <div class="form-group">
                        <label for="forumTitle">
                            Forum Title <span class="required">*</span>
                        </label>
                        <input type="text" id="forumTitle" placeholder="e.g., Introduction to HCI" required>
                        <small>Choose a clear and descriptive title</small>
                    </div>

                    <div class="form-group">
                        <label for="forumDescription">
                            Description <span class="required">*</span>
                        </label>
                        <textarea id="forumDescription" rows="4" placeholder="Describe what this forum is about..." required minlength="20"></textarea>
                        <small>Minimum 20 characters. Be clear and detailed.</small>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Privacy & Visibility</div>
                    
                    <div class="form-group">
                        <label for="forumVisibility">
                            Visibility
                        </label>
                        <select id="forumVisibility" onchange="handleVisibilityChange()">
                            <option value="public">Public - Anyone can view and post</option>
                            <option value="class">Class Only - Only class members</option>
                            <option value="specific">Specific Members - Invite only</option>
                        </select>
                        <small>Control who can access this forum</small>
                    </div>

                    <div class="form-group" id="classSelectionGroup" style="display: none;">
                        <label for="selectedClass">
                            Select Classroom <span class="required">*</span>
                        </label>
                        <select id="selectedClass">
                            <option value="">Choose a classroom...</option>
                        </select>
                        <small>This forum will only be visible to members of the selected class</small>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Schedule (Optional)</div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="forumStartDate">Start Date</label>
                            <input type="datetime-local" id="forumStartDate">
                            <small>When should this forum become active?</small>
                        </div>

                        <div class="form-group">
                            <label for="forumEndDate">End Date</label>
                            <input type="datetime-local" id="forumEndDate">
                            <small>When should this forum close?</small>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn-cancel" onclick="window.location.href='forum.html'">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-check"></i> Create Forum
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('assets/js/create-forum.js') }}"></script>
</body>
</html>


