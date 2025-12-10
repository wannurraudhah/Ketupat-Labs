<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Ketupat Labs')</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4F46E5;
            --primary-dark: #4338CA;
            --secondary: #6B7280;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --light: #F9FAFB;
            --dark: #111827;
            --border: #E5E7EB;
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        body {
            background-color: #f8fafc;
            color: #374151;
            line-height: 1.6;
        }
        
        /* Color utility classes */
        .bg-blue-light { background-color: #DBEAFE !important; }
        .bg-purple-light { background-color: #EDE9FE !important; }
        .bg-green-light { background-color: #D1FAE5 !important; }
        .bg-orange-light { background-color: #FEF3C7 !important; }
        .bg-yellow-light { background-color: #FEF3C7 !important; }
        .bg-gray-light { background-color: #F3F4F6 !important; }

.text-blue { color: #3B82F6 !important; }
.text-purple { color: #8B5CF6 !important; }
.text-green { color: #10B981 !important; }
.text-orange { color: #F59E0B !important; }
.text-yellow { color: #FBBF24 !important; }
.text-gray { color: #6B7280 !important; }
        /* Navigation */
        .navbar {
            background: white;
            border-bottom: 1px solid var(--border);
            box-shadow: var(--shadow);
            padding: 0.75rem 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.25rem;
        }
        
        .nav-link {
            font-weight: 500;
            color: #6B7280;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.2s;
            margin: 0 0.125rem;
        }
        
        .nav-link:hover {
            color: var(--primary);
            background: #f3f4f6;
        }
        
        .nav-link.active {
            color: var(--primary);
            background: rgba(79, 70, 229, 0.1);
            font-weight: 600;
        }
        
        /* Cards */
        .card {
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: var(--shadow);
            transition: transform 0.2s, box-shadow 0.2s;
            background: white;
        }
        
        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.25rem;
            font-weight: 600;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
        }
        
        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary);
            border-color: var(--primary);
        }
        
        /* Badges */
        .badge {
            border-radius: 20px;
            padding: 0.35rem 0.75rem;
            font-weight: 500;
            font-size: 0.75rem;
        }
        
        /* Forms */
        .form-control, .form-select {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        /* Tables */
        .table {
            --bs-table-bg: transparent;
            --bs-table-striped-bg: #f9fafb;
        }
        
        .table th {
            font-weight: 600;
            color: #6B7280;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-top: none;
        }
        
        .table td {
            padding: 1rem 0.75rem;
            border-color: var(--border);
            vertical-align: middle;
        }
        
        /* Progress bars */
        .progress {
            background-color: #f3f4f6;
            border-radius: 10px;
            height: 8px;
        }
        
        .progress-bar {
            border-radius: 10px;
        }
        
        /* Container */
        .container-fluid {
            max-width: 1400px;
        }
        
        /* Footer */
        footer {
            background: white;
            border-top: 1px solid var(--border);
            margin-top: auto;
        }
        
        /* Custom utilities */
        .rounded-xl {
            border-radius: 12px;
        }
        
        .text-muted {
            color: #9CA3AF !important;
        }
        
        .bg-light {
            background-color: #f9fafb !important;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
        }
        
        .transition-all {
            transition: all 0.2s ease;
        }
    </style>
    
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                     style="width: 32px; height: 32px;">
                    <i class="fas fa-certificate text-white" style="font-size: 0.875rem;"></i>
                </div>
                <span>Ketupat Labs</span>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">
                        <i class="fas fa-home me-1"></i> Laman Utama
                    </a>
                    <a class="nav-link {{ request()->is('badges*') ? 'active' : '' }}" href="/badges">
                        <i class="fas fa-award me-1"></i> Lencana
                    </a>
                    <a class="nav-link {{ request()->is('students*') ? 'active' : '' }}" href="/students">
                        <i class="fas fa-users me-1"></i> Pelajar
                    </a>
                    <a class="nav-link {{ request()->is('categories*') ? 'active' : '' }}" href="/categories">
                        <i class="fas fa-layer-group me-1"></i> Kategori
                    </a>
                </div>
                
                <!-- Search -->
                <form class="d-flex ms-3" action="/badges" method="GET" style="max-width: 240px;">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" 
                               name="search" placeholder="Search badges..." 
                               value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="flex-grow-1 py-4">
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 24px; height: 24px;">
                            <i class="fas fa-certificate text-white" style="font-size: 0.75rem;"></i>
                        </div>
                        <span class="text-muted">Ketupat Labs</span>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        Ketupat Labs' Project • {{ date('Y') }}
                    </small>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add active state to current page
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>