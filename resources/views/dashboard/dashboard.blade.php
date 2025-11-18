<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Pemuka - CompuPlay</title>
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="{{ asset('assets/images/LOGOCompuPlay.png') }}" alt="CompuPlay Logo" class="logo-img" onerror="this.onerror=null; this.src='{{ asset('assets/images/LOGOCompuPlay.svg') }}'">
                    <span class="logo-text">CompuPlay</span>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li>
                        <a href="#dashboard" class="nav-item active" data-page="dashboard">
                            <i class="fas fa-home"></i>
                            <span>Papan Pemuka</span>
                        </a>
                    </li>
                    <li>
                        <a href="#classes" class="nav-item" data-page="classes">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>Kelas</span>
                        </a>
                    </li>
                    <li>
                        <a href="#students" class="nav-item" data-page="students">
                            <i class="fas fa-user-graduate"></i>
                            <span>Pelajar</span>
                        </a>
                    </li>
                    <li>
                        <a href="#lessons" class="nav-item" data-page="lessons">
                            <i class="fas fa-book-open"></i>
                            <span>Pelajaran</span>
                        </a>
                    </li>
                    <li>
                        <a href="#assign-lessons" class="nav-item" data-page="assign-lessons">
                            <i class="fas fa-tasks"></i>
                            <span>Tugasan Pelajaran</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('forum.index') }}" class="nav-item">
                            <i class="fas fa-comments"></i>
                            <span>Forum</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('messaging') }}" class="nav-item">
                            <i class="fas fa-envelope"></i>
                            <span>Mesej</span>
                            <span class="badge" id="unreadMessagesBadge">0</span>
                        </a>
                    </li>
                    <li>
                        <a href="#notifications" class="nav-item" data-page="notifications">
                            <i class="fas fa-bell"></i>
                            <span>Notifikasi</span>
                            <span class="badge" id="unreadNotificationsBadge">0</span>
                        </a>
                    </li>
                    <li>
                        <a href="#performance" class="nav-item" data-page="performance">
                            <i class="fas fa-chart-line"></i>
                            <span>Prestasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="#progress" class="nav-item" data-page="progress">
                            <i class="fas fa-tasks"></i>
                            <span>Kemajuan</span>
                        </a>
                    </li>
                    <li>
                        <a href="#documents" class="nav-item" data-page="documents">
                            <i class="fas fa-file-alt"></i>
                            <span>Dokumen</span>
                        </a>
                    </li>
                    <li>
                        <a href="#generate-notes" class="nav-item" data-page="generate-notes">
                            <i class="fas fa-robot"></i>
                            <span>Jana Nota AI</span>
                        </a>
                    </li>
                    <li>
                        <a href="#ask-ai" class="nav-item" data-page="ask-ai">
                            <i class="fas fa-question-circle"></i>
                            <span>Tanya AI</span>
                        </a>
                    </li>
                    <li>
                        <a href="#settings" class="nav-item" data-page="settings">
                            <i class="fas fa-cog"></i>
                            <span>Tetapan</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar" id="userAvatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-details">
                        <span class="user-name" id="userName">Memuatkan...</span>
                        <span class="user-role" id="userRole">Cikgu</span>
                    </div>
                </div>
                <a href="#" class="logout-btn" id="logoutBtn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Keluar</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <button class="mobile-menu-toggle" id="mobileMenuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title" id="pageTitle">Papan Pemuka</h1>
                </div>
                <div class="header-right">
                    <div class="header-actions">
                        <button class="header-btn" id="notificationsBtn" title="Notifikasi">
                            <i class="fas fa-bell"></i>
                            <span class="badge" id="headerNotificationsBadge">0</span>
                        </button>
                        <button class="header-btn" id="messagesBtn" title="Mesej">
                            <i class="fas fa-envelope"></i>
                            <span class="badge" id="headerMessagesBadge">0</span>
                        </button>
                    </div>
                    <div class="user-menu">
                        <div class="user-menu-trigger" id="userMenuTrigger">
                            <div class="user-avatar-small" id="userAvatarSmall">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="user-name-small" id="userNameSmall">Memuatkan...</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="user-menu-dropdown" id="userMenuDropdown">
                            <a href="#profile" class="dropdown-item">
                                <i class="fas fa-user"></i> Profil
                            </a>
                            <a href="#settings" class="dropdown-item">
                                <i class="fas fa-cog"></i> Tetapan
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item" id="logoutDropdownBtn">
                                <i class="fas fa-sign-out-alt"></i> Log Keluar
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="content-area" id="contentArea">
                <!-- Dashboard Page -->
                <div class="page-content active" id="page-dashboard">
                    <div class="dashboard-overview">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon" style="background: linear-gradient(135deg, #2454FF, #5FAD56);">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-value" id="totalClasses">0</h3>
                                    <p class="stat-label">Kelas</p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon" style="background: linear-gradient(135deg, #5FAD56, #2454FF);">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-value" id="totalStudents">0</h3>
                                    <p class="stat-label">Pelajar</p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon" style="background: linear-gradient(135deg, #F26430, #FFBA08);">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-value" id="totalLessons">0</h3>
                                    <p class="stat-label">Pelajaran</p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon" style="background: linear-gradient(135deg, #FFBA08, #F26430);">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-value" id="totalForums">0</h3>
                                    <p class="stat-label">Forum</p>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-sections">
                            <div class="dashboard-section">
                                <div class="section-header">
                                    <h2>Kelas Terkini</h2>
                                    <a href="#classes" class="view-all">Lihat Semua</a>
                                </div>
                                <div class="section-content" id="recentClasses">
                                    <div class="empty-state">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        <p>Memuatkan data...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="dashboard-section">
                                <div class="section-header">
                                    <h2>Pelajaran Terkini</h2>
                                    <a href="#lessons" class="view-all">Lihat Semua</a>
                                </div>
                                <div class="section-content" id="recentLessons">
                                    <div class="empty-state">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        <p>Memuatkan data...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="dashboard-section">
                                <div class="section-header">
                                    <h2>Aktiviti Terkini</h2>
                                </div>
                                <div class="section-content" id="recentActivity">
                                    <div class="empty-state">
                                        <i class="fas fa-clock"></i>
                                        <p>Tiada aktiviti terkini</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Classes Page -->
                <div class="page-content" id="page-classes">
                    <div class="page-header-actions">
                        <button class="btn-primary" id="createClassBtn">
                            <i class="fas fa-plus"></i> Cipta Kelas Baru
                        </button>
                    </div>
                    <div class="classes-list" id="classesList">
                        <div class="empty-state">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Memuatkan kelas...</p>
                        </div>
                    </div>
                </div>

                <!-- Students Page -->
                <div class="page-content" id="page-students">
                    <div class="students-container" id="studentsContainer">
                        <div class="empty-state">
                            <i class="fas fa-user-graduate"></i>
                            <p>Pilih kelas untuk melihat pelajar</p>
                        </div>
                    </div>
                </div>

                <!-- Lessons Page -->
                <div class="page-content" id="page-lessons">
                    <div class="page-header-actions">
                        <button class="btn-primary" id="createLessonBtn">
                            <i class="fas fa-plus"></i> Cipta Pelajaran Baru
                        </button>
                    </div>
                    <div class="lessons-list" id="lessonsList">
                        <div class="empty-state">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Memuatkan pelajaran...</p>
                        </div>
                    </div>
                </div>

                <!-- Assign Lessons Page -->
                <div class="page-content" id="page-assign-lessons">
                    <div class="assign-lessons-container" id="assignLessonsContainer">
                        <div class="empty-state">
                            <i class="fas fa-tasks"></i>
                            <p>Memuatkan data...</p>
                        </div>
                    </div>
                </div>

                <!-- Notifications Page -->
                <div class="page-content" id="page-notifications">
                    <div class="notifications-container" id="notificationsContainer">
                        <div class="empty-state">
                            <i class="fas fa-bell"></i>
                            <p>Memuatkan notifikasi...</p>
                        </div>
                    </div>
                </div>

                <!-- Performance Page -->
                <div class="page-content" id="page-performance">
                    <div class="performance-container" id="performanceContainer">
                        <div class="empty-state">
                            <i class="fas fa-chart-line"></i>
                            <p>Memuatkan data prestasi...</p>
                        </div>
                    </div>
                </div>

                <!-- Progress Page -->
                <div class="page-content" id="page-progress">
                    <div class="progress-container" id="progressContainer">
                        <div class="empty-state">
                            <i class="fas fa-tasks"></i>
                            <p>Memuatkan data kemajuan...</p>
                        </div>
                    </div>
                </div>

                <!-- Documents Page -->
                <div class="page-content" id="page-documents">
                    <div class="documents-container" id="documentsContainer">
                        <div class="empty-state">
                            <i class="fas fa-file-alt"></i>
                            <p>Memuatkan dokumen...</p>
                        </div>
                    </div>
                </div>

                <!-- Generate Notes Page -->
                <div class="page-content" id="page-generate-notes">
                    <div class="generate-notes-container" id="generateNotesContainer">
                        <div class="empty-state">
                            <i class="fas fa-robot"></i>
                            <p>Memuatkan...</p>
                        </div>
                    </div>
                </div>

                <!-- Ask AI Page -->
                <div class="page-content" id="page-ask-ai">
                    <div class="ask-ai-container" id="askAiContainer">
                        <div class="empty-state">
                            <i class="fas fa-robot"></i>
                            <p>Memuatkan AI Chatbot...</p>
                        </div>
                    </div>
                </div>

                <!-- Settings Page -->
                <div class="page-content" id="page-settings">
                    <div class="settings-container" id="settingsContainer">
                        <div class="empty-state">
                            <i class="fas fa-cog"></i>
                            <p>Tetapan</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals will be added here via JavaScript -->
    <div id="modalContainer"></div>

    <script src="{{ asset('assets/js/api-config.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
</body>
</html>


