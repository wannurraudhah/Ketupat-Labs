<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post - Material Forum</title>
    <link rel="stylesheet" href="{{ asset('assets/css/forum.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/create-post.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css') }}">
</head>
<body>
    <header class="create-post-header">
        <div class="header-left">
            <a href="forum.html" class="logo">
                <i class="fas fa-comments"></i>
                <span>Material Forum</span>
            </a>
            <button class="back-btn" onclick="goBack()">
                <i class="fas fa-arrow-left"></i>
                Back
            </button>
        </div>
    </header>

    <div class="create-post-container">
        <div class="create-post-card">
            <div class="card-header">
                <h2>Create New Post</h2>
                <p>Share your thoughts, ask questions, or start a discussion</p>
            </div>

            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle"></i> Post created successfully!
            </div>

            <div class="error-message" id="errorMessage"></div>

            <form id="createPostForm">
                <div class="forum-selection">
                    <h3>Select Forum</h3>
                    <select id="forumSelect" class="form-group" style="width: 100%; padding: 10px;" required>
                        <option value="">Choose a forum...</option>
                    </select>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Post Type</div>
                    
                    <div class="category-grid">
                        <label class="category-option" id="postTypePost">
                            <input type="radio" name="postType" value="post" checked>
                            <span>Text Post</span>
                        </label>
                        <label class="category-option" id="postTypeLink">
                            <input type="radio" name="postType" value="link">
                            <span>Link</span>
                        </label>
                        <label class="category-option" id="postTypePoll">
                            <input type="radio" name="postType" value="poll">
                            <span>Poll</span>
                        </label>
                    </div>
                </div>

                <div class="form-section" id="postDetailsSection">
                    <div class="form-section-title">Post Details</div>
                    
                    <div class="form-group">
                        <label for="postTitle">
                            Post Title <span class="required">*</span>
                        </label>
                        <input type="text" id="postTitle" placeholder="Enter a clear and descriptive title" required>
                        <small>Be specific about what you're asking or discussing</small>
                    </div>

                    <div class="form-group" id="contentGroup">
                        <label for="postContent">
                            Content <span class="required">*</span>
                        </label>
                        <textarea id="postContent" rows="8" placeholder="Write your post content here..." required minlength="10"></textarea>
                        <small>Minimum 10 characters. Be clear and detailed in your explanation.</small>
                    </div>

                    <div class="form-group" id="linkGroup" style="display: none;">
                        <label for="postLink">
                            URL Link <span class="required">*</span>
                        </label>
                        <input type="url" id="postLink" placeholder="https://example.com">
                        <small>Enter the full URL you want to share</small>
                    </div>

                    <div class="form-group" id="pollGroup" style="display: none;">
                        <label for="pollOptions">
                            Poll Options <span class="required">*</span>
                        </label>
                        <div id="pollOptionsContainer"></div>
                        <button type="button" id="addPollOption" class="btn-secondary" style="display: none;">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                        <small>Add at least 2 options for your poll</small>
                    </div>
                </div>

                <div class="form-section" id="attachmentSection">
                    <div class="form-section-title">Attachments (Optional)</div>
                    
                    <div class="form-group">
                        <input type="file" id="attachmentInput" multiple>
                        <div id="attachmentsPreview" class="attachments-preview"></div>
                        <small>Upload files of any type. Maximum file size: 10MB each.</small>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Tags (Optional)</div>
                    
                    <div class="form-group">
                        <label for="tagsInput">Tags</label>
                        <input type="text" id="tagsInput" placeholder="Add tags (press Enter)" onkeypress="handleTagInput(event)">
                        <div class="tag-input-container" id="tagsContainer"></div>
                        <small>Press Enter to add a tag. Click the X to remove.</small>
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn-cancel" onclick="goBack()">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-check"></i> Create Post
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('assets/js/create-post.js') }}"></script>
</body>
</html>


