<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Details - Material Forum</title>
    <link rel="stylesheet" href="{{ asset('assets/css/forum.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/forum-detail.css') }}">
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
                <button class="btn-create-post" id="btnCreatePost" title="Create Post">
                    <i class="fas fa-edit"></i> Create Post
                </button>
                <button class="btn-create-post" id="btnLogout" title="Logout" style="background-color: #ff4500; margin-left: 8px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </header>

        <div class="forum-page-header">
            <a href="forum.html" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to Forums
            </a>
            <div class="forum-page-header-content">
                <div class="forum-page-avatar" id="forumAvatar">
                    <span>?</span>
                </div>
                <div class="forum-page-info">
                    <div class="forum-page-title" id="forumName">Loading...</div>
                    <div class="forum-page-subtitle" id="forumSubtitle">Forum</div>
                </div>
                <div class="forum-header-actions">
                    <button class="btn-create-post-forum" id="btnCreatePostForForum" style="display: none;">
                        <i class="fas fa-plus"></i>
                        Create Post
                    </button>
                    <button class="btn-join" id="btnJoin" style="display: none;">
                        Join
                    </button>
                    <button class="btn-joined" id="btnJoined" style="display: none;">
                        Joined
                    </button>
                    <div class="more-menu-container">
                        <button class="btn-more-dots" id="btnMore">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="more-menu" id="moreMenu">
                            <div class="more-menu-item" id="muteOption">
                                <i class="fas fa-bell-slash"></i>
                                <span id="muteText">Mute Forum</span>
                            </div>
                            <div class="more-menu-item" id="favoriteOption">
                                <i class="far fa-bookmark"></i>
                                <span id="favoriteText">Add to Favorites</span>
                            </div>
                            <div class="more-menu-item" id="manageOption" style="display: none;">
                                <i class="fas fa-cog"></i>
                                <span>Manage Forum</span>
                            </div>
                            <div class="more-menu-item danger" id="leaveOption">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Leave Forum</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="reddit-main-forum">
            <div class="reddit-main-forum-content">
                <div class="sort-controls-forum">
                    <select id="sortPosts">
                        <option value="recent">Hot</option>
                        <option value="popular">New</option>
                        <option value="top">Top</option>
                        <option value="rising">Rising</option>
                    </select>
                    <div class="view-toggle">
                        <button class="view-toggle-btn" title="Card View">
                            <i class="fas fa-th"></i>
                        </button>
                    </div>
                </div>
                
                <main class="reddit-content" id="postsContent">
                </main>
            </div>

            <aside class="reddit-main-forum-sidebar">
                <div class="forum-sidebar-card">
                    <div class="forum-sidebar-card-header">
                        ABOUT COMMUNITY
                    </div>
                    <div class="forum-sidebar-card-body">
                        <div class="forum-description" id="forumDescription">
                            No description available.
                        </div>
                        
                        <div class="forum-meta">
                            <div class="forum-meta-item">
                                <i class="fas fa-home"></i>
                                <span>Created <span id="createdDate">-</span></span>
                            </div>
                            <div class="forum-meta-item">
                                <i class="fas fa-globe"></i>
                                <span id="forumVisibility">Public</span>
                            </div>
                        </div>

                        <div class="forum-stats-grid">
                            <div class="forum-stat-big">
                                <div class="forum-stat-value" id="memberCount">0</div>
                                <div class="forum-stat-label">Members</div>
                            </div>
                            <div class="forum-stat-big">
                                <div class="forum-stat-value" id="postCount">0</div>
                                <div class="forum-stat-label">Online</div>
                            </div>
                        </div>

                        <button class="btn-message-mods" style="margin-top: 16px;">
                            <i class="fas fa-comment"></i>
                            Message Mods
                        </button>
                    </div>
                </div>

            </aside>
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

    <script src="{{ asset('assets/js/forum-detail.js') }}"></script>
</body>
</html>

