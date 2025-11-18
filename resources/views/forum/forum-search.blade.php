<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Forums - Material Forum</title>
    <link rel="stylesheet" href="{{ asset('assets/css/forum.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/forum-search.css') }}">
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
            <div class="header-center">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search forums...">
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
            <div class="search-results-header">
                <a href="forum.html" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Forums
                </a>
                <h1 id="searchTitle">Search Results</h1>
            </div>

            <main class="reddit-content" id="searchResults">
            </main>
        </div>
    </div>

    <script src="{{ asset('assets/js/forum-search.js') }}"></script>
</body>
</html>


