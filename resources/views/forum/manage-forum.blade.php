<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Forum - Material Forum</title>
    <link rel="stylesheet" href="{{ asset('assets/css/forum.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/manage-forum.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css') }}">
</head>
<body>
    <div class="reddit-container">
        <header class="reddit-header">
            <div class="header-left">
                <div class="logo">
                    <i class="fas fa-comments"></i>
                    <span class="logo-text">Material Forum</span>
                </div>
            </div>
            <div class="header-right">
                <button class="btn-create-post" id="btnLogout" title="Logout" style="background-color: #ff4500;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </header>

        <div class="manage-forum-header">
            <a href="#" class="back-link" id="backLink">
                <i class="fas fa-arrow-left"></i>
                Back to Forum
            </a>
            <h1>Manage Forum</h1>
        </div>

        <div class="manage-container">
            <div class="success-message" id="successMessage"></div>
            <div class="error-message" id="errorMessage"></div>

            <div class="manage-grid">
                <div>
                    <div class="manage-card">
                        <div class="manage-card-header">
                            <i class="fas fa-users"></i> Forum Members
                        </div>
                        <div class="members-list" id="membersList">
                            <!-- Members will be loaded here -->
                        </div>
                    </div>
                </div>

                <div>
                    <div class="manage-card">
                        <div class="manage-card-header">
                            <i class="fas fa-cog"></i> Forum Settings
                        </div>
                        
                        <form id="settingsForm">
                            <div class="form-group">
                                <label for="forumTitle">Forum Title</label>
                                <input type="text" id="forumTitle" required>
                            </div>

                            <div class="form-group">
                                <label for="forumDescription">Description</label>
                                <textarea id="forumDescription" rows="6"></textarea>
                            </div>

                            <div class="action-buttons">
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="manage-card">
                        <div class="manage-card-header">
                            <i class="fas fa-exclamation-triangle"></i> Danger Zone
                        </div>
                        <button class="btn-danger" onclick="showDeleteConfirm()">
                            <i class="fas fa-trash-alt"></i> Delete Forum
                        </button>
                        <small style="display: block; margin-top: 8px; color: #878a8c; font-size: 12px;">
                            This action cannot be undone
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/manage-forum.js') }}"></script>
</body>
</html>


