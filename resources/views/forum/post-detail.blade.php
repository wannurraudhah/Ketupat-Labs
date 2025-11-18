<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Details - Material Forum</title>
    <link rel="stylesheet" href="{{ asset('assets/css/forum.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/forum-detail.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/post-detail.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css') }}">
</head>
<body>
    <div class="reddit-container">
        <header class="reddit-header">
            <div class="header-left">
                <a href="forum.html" class="logo" style="text-decoration: none;">
                    <i class="fas fa-comments"></i>
                    <span class="logo-text">Material Forum</span>
                </a>
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
            </aside>

            <main class="reddit-content">
                <div class="post-detail-content" id="postDetailContent">
                    <!-- Post content will be loaded here -->
                </div>
            </main>

            <aside class="reddit-main-forum-sidebar">
                <div class="forum-sidebar-card">
                    <div class="forum-sidebar-card-header">
                        ABOUT COMMUNITY
                    </div>
                    <div class="forum-sidebar-card-body" id="aboutCommunity">
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script src="{{ asset('assets/js/post-detail.js') }}"></script>
</body>
</html>

