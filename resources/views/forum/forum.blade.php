<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum - Material Learning Platform</title>
    <link rel="stylesheet" href="{{ asset('assets/css/forum.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <div class="header-center">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchForums" placeholder="Search forums...">
                </div>
            </div>
            <div class="header-right">
                <button class="btn-create-post" id="btnCreatePost" title="Create Post">
                    <i class="fas fa-edit"></i> Create Post
                </button>
                <button class="btn-create-post" id="btnLogout" title="Logout" style="background-color: #ff4500; margin-left: 8px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </header>

        <div class="reddit-main">
            <aside class="reddit-sidebar">
                <div class="sidebar-section">
                    <button class="btn-create-forum" id="btnCreateForum">
                        <i class="fas fa-plus-circle"></i> Create Forum
                    </button>
                </div>
                
                <div class="sidebar-section">
                    <h3 class="sidebar-title">My Forums</h3>
                    <div class="filter-list" id="forumsList">
                        <!-- Forums will be loaded here -->
                    </div>
                </div>
                
                <div class="sidebar-section">
                    <h3 class="sidebar-title">Popular Tags</h3>
                    <div class="tag-cloud" id="tagCloud">
                    </div>
                </div>

                <div class="sidebar-section">
                    <div class="sort-controls">
                        <label for="sortPosts" class="sort-label">Sort by:</label>
                        <select id="sortPosts" class="sort-select">
                            <option value="recent">Most Recent</option>
                            <option value="popular">Most Popular</option>
                        </select>
                    </div>
                </div>
            </aside>

            <main class="reddit-content" id="forumsContent">
            </main>

            <aside class="reddit-sidebar-right">
                <div class="sidebar-section">
                    <div class="sidebar-header">
                        <h3 class="sidebar-title">Recent Posts</h3>
                        <button class="btn-clear-recent" onclick="clearRecentPosts()" title="Clear">
                            Clear
                        </button>
                    </div>
                    <div class="recent-posts-list" id="recentPostsList">
                        <p style="padding: 16px; color: #878a8c; font-size: 12px; text-align: center;">No recent posts</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <div class="modal" id="createForumModal">
        <div class="modal-content large">
            <div class="modal-header">
                <h2>Create New Forum</h2>
                <button class="modal-close" id="closeForumModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="createForumForm">
                    <div class="form-group">
                        <label for="forumTitle">Forum Title <span class="required">*</span></label>
                        <input type="text" id="forumTitle" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="forumDescription">Description <span class="required">*</span></label>
                        <textarea id="forumDescription" rows="4" required placeholder="Minimum 20 characters"></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="forumTags">Tags</label>
                            <input type="text" id="forumTags" placeholder="Separate with commas">
                        </div>
                        <div class="form-group">
                            <label for="forumVisibility">Visibility</label>
                            <select id="forumVisibility">
                                <option value="public">Public</option>
                                <option value="class">Class Only</option>
                                <option value="specific">Specific Members</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="forumStartDate">Start Date (Optional)</label>
                            <input type="datetime-local" id="forumStartDate">
                        </div>
                        <div class="form-group">
                            <label for="forumEndDate">End Date (Optional)</label>
                            <input type="datetime-local" id="forumEndDate">
                        </div>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="btn-cancel" id="cancelForumModal">Cancel</button>
                        <button type="submit" class="btn-primary">Create Forum</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="postDetailModal">
        <div class="modal-content large">
            <div class="modal-header">
                <button class="modal-close" id="closePostModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="postDetailContent">
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/forum.js') }}"></script>
</body>
</html>


