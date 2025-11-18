// Dashboard JavaScript

// Global state
const dashboardState = {
    currentUser: null,
    currentPage: 'dashboard',
    classes: [],
    students: [],
    lessons: [],
    notifications: [],
    unreadNotifications: 0,
    unreadMessages: 0
};

// Initialize dashboard
document.addEventListener('DOMContentLoaded', async function() {
    // Check authentication first, but don't block if sessionStorage indicates login
    const userLoggedIn = sessionStorage.getItem('userLoggedIn');
    const userId = sessionStorage.getItem('userId');
    
    if (userLoggedIn === 'true' && userId) {
        // User appears to be logged in from sessionStorage
        // Load dashboard immediately, then verify with server in background
        initializeNavigation();
        initializeUserMenu();
        initializeMobileMenu();
        loadUserInfo();
        loadDashboardData();
        setupEventListeners();
        
        // Verify authentication in background
        checkAuthentication().catch(err => {
            console.error('Background auth check failed:', err);
        });
    } else {
        // No sessionStorage, check with server
        await checkAuthentication();
        initializeNavigation();
        initializeUserMenu();
        initializeMobileMenu();
        loadUserInfo();
        loadDashboardData();
        setupEventListeners();
    }
});

// Check if user is authenticated
async function checkAuthentication() {
    // First check sessionStorage - if user just logged in, they should be redirected
    const userLoggedIn = sessionStorage.getItem('userLoggedIn');
    const userId = sessionStorage.getItem('userId');
    
    if (userLoggedIn === 'true' && userId) {
        console.log('User found in sessionStorage, verifying with server...');
    }
    
    try {
        // Make a direct fetch to avoid error throwing for /me endpoint
        const response = await fetch(API_ENDPOINTS.me, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'include',
        });
        
        const data = await response.json();
        console.log('Auth check response:', { status: response.status, data });
        
        if (data.status === 200 && data.data) {
            // User is authenticated, store user info
            sessionStorage.setItem('userLoggedIn', 'true');
            sessionStorage.setItem('userId', data.data.user_id);
            sessionStorage.setItem('userEmail', data.data.email);
            sessionStorage.setItem('userName', data.data.name);
            sessionStorage.setItem('userRole', data.data.role);
            
            // Update UI with user info
            updateUserInfo(data.data);
        } else {
            // Not authenticated
            console.log('User not authenticated on server');
            // Don't immediately redirect if we have sessionStorage data
            // This might be a timing issue where session isn't ready yet
            const userLoggedIn = sessionStorage.getItem('userLoggedIn');
            if (!userLoggedIn || userLoggedIn !== 'true') {
                sessionStorage.clear();
                // Only redirect if we're not already on login page
                if (!window.location.pathname.includes('/login')) {
                    window.location.href = '/login';
                }
            } else {
                // We have sessionStorage but server doesn't recognize session
                // This might be a session sync issue - log it but don't redirect
                console.warn('Session mismatch: sessionStorage indicates login but server does not');
            }
        }
    } catch (error) {
        console.error('Auth check error:', error);
        // If API call fails, check if we have sessionStorage data
        // If we just logged in, give it a moment for the session to be available
        if (userLoggedIn === 'true' && userId) {
            console.log('API call failed but sessionStorage indicates login, waiting...');
            // Wait a bit and try again, or just continue (session might not be ready yet)
            setTimeout(async () => {
                try {
                    const retryResponse = await fetch(API_ENDPOINTS.me, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'include',
                    });
                    const retryData = await retryResponse.json();
                    if (retryData.status !== 200 || !retryData.data) {
                        // Still not authenticated, redirect to login
                        sessionStorage.clear();
                        window.location.href = '/login';
                    }
                } catch (retryError) {
                    // Still failing, redirect to login
                    sessionStorage.clear();
                    window.location.href = '/login';
                }
            }, 500);
        } else {
            // No sessionStorage data, redirect to login
            sessionStorage.clear();
            if (!window.location.pathname.includes('/login')) {
                window.location.href = '/login';
            }
        }
    }
}

// Initialize navigation
function initializeNavigation() {
    const navItems = document.querySelectorAll('.nav-item');
    const pageContents = document.querySelectorAll('.page-content');
    
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Check if this is an external link (Forum, Messaging)
            const href = this.getAttribute('href');
            if (href && (href.includes('Forum/') || href.includes('Messaging/'))) {
                // Allow default navigation for external links
                return;
            }
            
            e.preventDefault();
            
            // Check if it has data-page attribute (internal page)
            const page = this.getAttribute('data-page');
            if (!page) {
                return; // Not an internal page, allow default behavior
            }
            
            // Remove active class from all nav items
            navItems.forEach(nav => {
                // Only remove active from items with data-page attribute
                if (nav.getAttribute('data-page')) {
                    nav.classList.remove('active');
                }
            });
            
            // Add active class to clicked item
            this.classList.add('active');
            
            // Hide all page contents
            pageContents.forEach(content => content.classList.remove('active'));
            
            // Show selected page
            const selectedPage = document.getElementById(`page-${page}`);
            if (selectedPage) {
                selectedPage.classList.add('active');
                dashboardState.currentPage = page;
                
                // Update page title
                updatePageTitle(page);
                
                // Load page data
                loadPageData(page);
            }
            
            // Close mobile menu if open
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.remove('open');
            }
            
            // Update URL hash
            window.history.pushState(null, null, `#${page}`);
        });
    });
    
    // Handle hash changes (for direct links)
    window.addEventListener('hashchange', function() {
        const hash = window.location.hash.substring(1) || 'dashboard';
        const navItem = document.querySelector(`[data-page="${hash}"]`);
        if (navItem) {
            navItem.click();
        }
    });
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        const hash = window.location.hash.substring(1) || 'dashboard';
        const navItem = document.querySelector(`[data-page="${hash}"]`);
        if (navItem) {
            navItem.click();
        }
    });
    
    // Check for initial hash
    const initialHash = window.location.hash.substring(1) || 'dashboard';
    const initialNavItem = document.querySelector(`[data-page="${initialHash}"]`);
    if (initialNavItem) {
        initialNavItem.click();
    } else {
        // Default to papan pemuka
        const dashboardNav = document.querySelector('[data-page="dashboard"]');
        if (dashboardNav) {
            dashboardNav.click();
        }
    }
}

// Update page title
function updatePageTitle(page) {
    const titles = {
        'dashboard': 'Papan Pemuka',
        'classes': 'Kelas',
        'students': 'Pelajar',
        'lessons': 'Pelajaran',
        'assign-lessons': 'Tugasan Pelajaran',
        'forum': 'Forum',
        'messages': 'Mesej',
        'notifications': 'Notifikasi',
        'performance': 'Prestasi',
        'progress': 'Kemajuan',
        'documents': 'Dokumen',
        'generate-notes': 'Jana Nota AI',
        'ask-ai': 'Tanya AI',
        'settings': 'Tetapan'
    };
    
    const pageTitle = document.getElementById('pageTitle');
    if (pageTitle) {
        pageTitle.textContent = titles[page] || 'Papan Pemuka';
    }
}

// Initialize user menu
function initializeUserMenu() {
    const userMenuTrigger = document.getElementById('userMenuTrigger');
    const userMenuDropdown = document.getElementById('userMenuDropdown');
    
    if (userMenuTrigger && userMenuDropdown) {
        userMenuTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenuDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenuTrigger.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                userMenuDropdown.classList.remove('show');
            }
        });
    }
    
    // Logout handlers
    const logoutBtn = document.getElementById('logoutBtn');
    const logoutDropdownBtn = document.getElementById('logoutDropdownBtn');
    
    if (logoutBtn) {
        logoutBtn.addEventListener('click', handleLogout);
    }
    
    if (logoutDropdownBtn) {
        logoutDropdownBtn.addEventListener('click', handleLogout);
    }
}

// Initialize mobile menu
function initializeMobileMenu() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            if (sidebar) {
                sidebar.classList.toggle('open');
            }
        });
    }
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            if (sidebar) {
                sidebar.classList.remove('open');
            }
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (sidebar && sidebar.classList.contains('open')) {
                if (!sidebar.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            }
        }
    });
}

// Update user info in UI
function updateUserInfo(userData) {
    const userName = userData.name || userData.email || 'Pengguna';
    const userRole = userData.role || 'pelajar';
    
    // Update UI elements
    const userNameElements = document.querySelectorAll('#userName, #userNameSmall');
    userNameElements.forEach(el => {
        if (el) el.textContent = userName;
    });
    
    const userRoleElement = document.getElementById('userRole');
    if (userRoleElement) {
        userRoleElement.textContent = userRole === 'cikgu' ? 'Cikgu' : 'Pelajar';
    }
    
    // Hide teacher-only features for students if needed
    if (userRole !== 'cikgu') {
        // Students can view dashboard but may have limited access
        // You can hide certain navigation items or features here if needed
    }
    
    // TODO: Load user avatar if available
    // const avatarUrl = userData.avatar_url;
    // if (avatarUrl) {
    //     const avatarElements = document.querySelectorAll('#userAvatar, #userAvatarSmall');
    //     avatarElements.forEach(el => {
    //         if (el) {
    //             el.innerHTML = `<img src="${avatarUrl}" alt="${userName}">`;
    //         }
    //     });
    // }
}

// Load user info
async function loadUserInfo() {
    try {
        const userId = sessionStorage.getItem('userId');
        const userEmail = sessionStorage.getItem('userEmail');
        const userName = sessionStorage.getItem('userName') || userEmail;
        const userRole = sessionStorage.getItem('userRole') || 'pelajar';
        
        dashboardState.currentUser = {
            id: userId,
            email: userEmail,
            name: userName,
            role: userRole
        };
        
        // Update UI
        updateUserInfo(dashboardState.currentUser);
        
    } catch (error) {
        console.error('Error loading user info:', error);
    }
}

// Load dashboard data
async function loadDashboardData() {
    await Promise.all([
        loadClasses(),
        loadLessons(),
        loadNotifications(),
        loadStats()
    ]);
}

// Load classes
async function loadClasses() {
    try {
        // TODO: Replace with actual Laravel API endpoint when available
        // For now, use placeholder data
        dashboardState.classes = [];
        renderClasses();
        renderRecentClasses();
    } catch (error) {
        console.error('Error loading classes:', error);
        const classesList = document.getElementById('classesList');
        if (classesList) {
            classesList.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Ralat memuatkan kelas. Sila cuba lagi.</p></div>';
        }
    }
}

// Render classes
function renderClasses() {
    const classesList = document.getElementById('classesList');
    if (!classesList) return;
    
    if (dashboardState.classes.length === 0) {
        classesList.innerHTML = '<div class="empty-state"><i class="fas fa-chalkboard-teacher"></i><p>Tiada kelas. Cipta kelas pertama anda!</p></div>';
        return;
    }
    
    classesList.innerHTML = dashboardState.classes.map(classItem => `
        <div class="class-card" data-class-id="${classItem.id}">
            <div class="class-card-header">
                <div>
                    <h3 class="class-card-title">${escapeHtml(classItem.name)}</h3>
                    <p class="class-card-subject">${escapeHtml(classItem.subject || 'Tiada')}</p>
                </div>
                <div class="class-card-actions">
                    <button class="class-card-action" onclick="editClass(${classItem.id})" title="Sunting">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="class-card-action" onclick="deleteClass(${classItem.id})" title="Padam">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="class-card-info">
                <div class="class-card-info-item">
                    <i class="fas fa-calendar"></i>
                    <span>${classItem.year || 'Tiada'}</span>
                </div>
                <div class="class-card-info-item">
                    <i class="fas fa-user-graduate"></i>
                    <span>Pelajar</span>
                </div>
            </div>
        </div>
    `).join('');
    
    // Add click event to class cards
    classesList.querySelectorAll('.class-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.closest('.class-card-actions')) {
                const classId = this.getAttribute('data-class-id');
                viewClassDetails(classId);
            }
        });
    });
}

// Render recent classes
function renderRecentClasses() {
    const recentClasses = document.getElementById('recentClasses');
    if (!recentClasses) return;
    
    const recent = dashboardState.classes.slice(0, 5);
    
    if (recent.length === 0) {
        recentClasses.innerHTML = '<div class="empty-state"><i class="fas fa-chalkboard-teacher"></i><p>Tiada kelas terkini</p></div>';
        return;
    }
    
    recentClasses.innerHTML = recent.map(classItem => `
        <div class="recent-item" onclick="viewClassDetails(${classItem.id})">
            <div class="recent-item-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="recent-item-content">
                <h4>${escapeHtml(classItem.name)}</h4>
                <p>${escapeHtml(classItem.subject || 'Tiada')}</p>
            </div>
            <div class="recent-item-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>
    `).join('');
}

// Load lessons
async function loadLessons() {
    try {
        // TODO: Implement lesson loading API
        // For now, use placeholder data
        dashboardState.lessons = [];
        renderLessons();
        renderRecentLessons();
    } catch (error) {
        console.error('Error loading lessons:', error);
    }
}

// Render lessons
function renderLessons() {
    const lessonsList = document.getElementById('lessonsList');
    if (!lessonsList) return;
    
    if (dashboardState.lessons.length === 0) {
        lessonsList.innerHTML = '<div class="empty-state"><i class="fas fa-book-open"></i><p>Tiada pelajaran. Cipta pelajaran pertama anda!</p></div>';
        return;
    }
    
    // TODO: Implement lesson rendering
}

// Render recent lessons
function renderRecentLessons() {
    const recentLessons = document.getElementById('recentLessons');
    if (!recentLessons) return;
    
    if (dashboardState.lessons.length === 0) {
        recentLessons.innerHTML = '<div class="empty-state"><i class="fas fa-book-open"></i><p>Tiada pelajaran terkini</p></div>';
        return;
    }
    
    // TODO: Implement recent lessons rendering
}

// Load notifications
async function loadNotifications() {
    try {
        // TODO: Implement notification loading API
        dashboardState.notifications = [];
        dashboardState.unreadNotifications = 0;
        updateNotificationBadges();
    } catch (error) {
        console.error('Error loading notifications:', error);
    }
}

// Update notification badges
function updateNotificationBadges() {
    const badges = document.querySelectorAll('#unreadNotificationsBadge, #headerNotificationsBadge');
    badges.forEach(badge => {
        if (badge) {
            badge.textContent = dashboardState.unreadNotifications;
            badge.style.display = dashboardState.unreadNotifications > 0 ? 'block' : 'none';
        }
    });
}

// Load stats
async function loadStats() {
    const totalClasses = document.getElementById('totalClasses');
    const totalStudents = document.getElementById('totalStudents');
    const totalLessons = document.getElementById('totalLessons');
    const totalForums = document.getElementById('totalForums');
    
    if (totalClasses) totalClasses.textContent = dashboardState.classes.length;
    if (totalStudents) totalStudents.textContent = '0'; // TODO: Calculate from classes
    if (totalLessons) totalLessons.textContent = dashboardState.lessons.length;
    if (totalForums) totalForums.textContent = '0'; // TODO: Load from API
}

// Load page data
function loadPageData(page) {
    switch (page) {
        case 'dashboard':
            loadDashboardData();
            break;
        case 'classes':
            loadClasses();
            break;
        case 'students':
            loadStudents();
            break;
        case 'lessons':
            loadLessons();
            break;
        case 'assign-lessons':
            loadAssignLessons();
            break;
        case 'notifications':
            loadNotifications();
            break;
        case 'performance':
            loadPerformance();
            break;
        case 'progress':
            loadProgress();
            break;
        case 'documents':
            loadDocuments();
            break;
        case 'generate-notes':
            loadGenerateNotes();
            break;
        case 'ask-ai':
            loadAskAI();
            break;
        case 'settings':
            loadSettings();
            break;
    }
}

// Load students
async function loadStudents() {
    const studentsContainer = document.getElementById('studentsContainer');
    if (!studentsContainer) return;
    
    studentsContainer.innerHTML = '<p class="empty-state">Pilih kelas untuk melihat pelajar</p>';
    // TODO: Implement student loading
}

// Load assign lessons
async function loadAssignLessons() {
    const container = document.getElementById('assignLessonsContainer');
    if (!container) return;
    
    container.innerHTML = '<p class="empty-state">Memuatkan data tugasan...</p>';
    // TODO: Implement assign lessons loading
}

// Load performance
async function loadPerformance() {
    const container = document.getElementById('performanceContainer');
    if (!container) return;
    
    container.innerHTML = '<p class="empty-state">Memuatkan data prestasi...</p>';
    // TODO: Implement performance loading
}

// Load progress
async function loadProgress() {
    const container = document.getElementById('progressContainer');
    if (!container) return;
    
    container.innerHTML = '<p class="empty-state">Memuatkan data kemajuan...</p>';
    // TODO: Implement progress loading
}

// Load documents
async function loadDocuments() {
    const container = document.getElementById('documentsContainer');
    if (!container) return;
    
    container.innerHTML = '<p class="empty-state">Memuatkan dokumen...</p>';
    // TODO: Implement document loading
}

// Load generate notes
async function loadGenerateNotes() {
    const container = document.getElementById('generateNotesContainer');
    if (!container) return;
    
    container.innerHTML = '<p class="empty-state">Memuatkan...</p>';
    // TODO: Implement generate notes UI
}

// Load settings
async function loadSettings() {
    const container = document.getElementById('settingsContainer');
    if (!container) return;
    
    container.innerHTML = '<p class="empty-state">Tetapan</p>';
    // TODO: Implement settings UI
}

// Setup event listeners
function setupEventListeners() {
    // Create class button
    const createClassBtn = document.getElementById('createClassBtn');
    if (createClassBtn) {
        createClassBtn.addEventListener('click', function() {
            // TODO: Open create class modal
            alert('Fungsi cipta kelas akan disediakan tidak lama lagi!');
        });
    }
    
    // Create lesson button
    const createLessonBtn = document.getElementById('createLessonBtn');
    if (createLessonBtn) {
        createLessonBtn.addEventListener('click', function() {
            // TODO: Open create lesson modal
            alert('Fungsi cipta pelajaran akan disediakan tidak lama lagi!');
        });
    }
    
    // Notifications button
    const notificationsBtn = document.getElementById('notificationsBtn');
    if (notificationsBtn) {
        notificationsBtn.addEventListener('click', function() {
            const navItem = document.querySelector('[data-page="notifications"]');
            if (navItem) {
                navItem.click();
            }
        });
    }
    
    // Messages button
    const messagesBtn = document.getElementById('messagesBtn');
    if (messagesBtn) {
        messagesBtn.addEventListener('click', function() {
            const navItem = document.querySelector('[data-page="messages"]');
            if (navItem) {
                navItem.click();
            }
        });
    }
}

// View class details
function viewClassDetails(classId) {
    // TODO: Implement class details view
    console.log('View class details:', classId);
    // Navigate to students page with class filter
    const navItem = document.querySelector('[data-page="students"]');
    if (navItem) {
        navItem.click();
    }
}

// Edit class
function editClass(classId) {
    // TODO: Implement edit class
    console.log('Edit class:', classId);
    alert('Fungsi edit kelas akan disediakan tidak lama lagi!');
}

// Delete class
function deleteClass(classId) {
    if (confirm('Adakah anda pasti mahu memadam kelas ini?')) {
        // TODO: Implement delete class
        console.log('Delete class:', classId);
        alert('Fungsi padam kelas akan disediakan tidak lama lagi!');
    }
}

// Handle logout
async function handleLogout(e) {
    e.preventDefault();
    
    if (confirm('Adakah anda pasti mahu log keluar?')) {
        try {
            // Call logout API
            await apiPost(API_ENDPOINTS.logout, {});
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            // Clear session storage
            sessionStorage.clear();
            
            // Redirect to login
            window.location.href = '/login';
        }
    }
}

// Load Ask AI page
async function loadAskAI() {
    const container = document.getElementById('askAiContainer');
    if (!container) return;
    
    container.innerHTML = `
        <div class="ask-ai-wrapper">
            <div class="ask-ai-header">
                <h2>Pembantu AI</h2>
                <p>Tanya soalan tentang pelajaran anda</p>
            </div>
            <div class="ask-ai-chat">
                <div class="chat-messages" id="aiChatMessages">
                    <div class="chat-message ai-message">
                        <p>Halo! Saya pembantu AI. Bagaimana saya boleh membantu anda hari ini?</p>
                    </div>
                </div>
                <div class="chat-input-wrapper">
                    <input type="text" id="aiChatInput" placeholder="Taip soalan anda di sini..." class="chat-input">
                    <button id="aiSendBtn" class="btn-primary">
                        <i class="fas fa-paper-plane"></i> Hantar
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Add event listeners for AI chat
    const aiChatInput = document.getElementById('aiChatInput');
    const aiSendBtn = document.getElementById('aiSendBtn');
    
    if (aiSendBtn) {
        aiSendBtn.addEventListener('click', sendAIMessage);
    }
    
    if (aiChatInput) {
        aiChatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendAIMessage();
            }
        });
    }
}

// Send AI message
async function sendAIMessage() {
    const input = document.getElementById('aiChatInput');
    const messagesContainer = document.getElementById('aiChatMessages');
    
    if (!input || !messagesContainer) return;
    
    const message = input.value.trim();
    if (!message) return;
    
    // Add user message
    const userMessage = document.createElement('div');
    userMessage.className = 'chat-message user-message';
    userMessage.innerHTML = `<p>${escapeHtml(message)}</p>`;
    messagesContainer.appendChild(userMessage);
    
    // Clear input
    input.value = '';
    
    // Show loading
    const loadingMessage = document.createElement('div');
    loadingMessage.className = 'chat-message ai-message loading';
    loadingMessage.innerHTML = '<p>Memproses...</p>';
    messagesContainer.appendChild(loadingMessage);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    try {
        // TODO: Call AI API endpoint
        // For now, show a placeholder response
        setTimeout(() => {
            loadingMessage.remove();
            const aiResponse = document.createElement('div');
            aiResponse.className = 'chat-message ai-message';
            aiResponse.innerHTML = `<p>Fungsi AI akan disediakan tidak lama lagi. Sila rujuk dokumentasi atau hubungi administrator.</p>`;
            messagesContainer.appendChild(aiResponse);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 1000);
    } catch (error) {
        loadingMessage.remove();
        const errorMessage = document.createElement('div');
        errorMessage.className = 'chat-message ai-message error';
        errorMessage.innerHTML = `<p>Ralat: ${escapeHtml(error.message)}</p>`;
        messagesContainer.appendChild(errorMessage);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

// Utility function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Utility function to format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('ms-MY', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Export functions for global use
window.viewClassDetails = viewClassDetails;
window.editClass = editClass;
window.deleteClass = deleteClass;

