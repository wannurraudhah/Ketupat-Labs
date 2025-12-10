{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badge Achievement System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold">🎖️ Badge Achievement System</h1>
            <p class="lead text-muted">Track your learning progress with achievement badges</p>
        </div>
        
        <div class="row g-4 mb-5">
            <div class="col-md-3 col-6">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="display-6 text-primary mb-2">
                            <i class="fas fa-award"></i>
                        </div>
                        <h3 class="fw-bold">{{ $badgeCount }}</h3>
                        <p class="text-muted mb-0">Available Badges</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="display-6 text-success mb-2">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h3 class="fw-bold">{{ $redeemedBadgeCount ?? 0 }}</h3>
                        <p class="text-muted mb-0">Badges Redeemed</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="display-6 text-info mb-2">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <h3 class="fw-bold">{{ $categoryCount }}</h3>
                        <p class="text-muted mb-0">Categories</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="display-6 text-warning mb-2">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="fw-bold">{{ $studentCount }}</h3>
                        <p class="text-muted mb-0">Students</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center">
            <a href="/badges" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-award me-2"></i>View All Badges
            </a>
            <a href="/my-badges" class="btn btn-outline-primary btn-lg px-5 ms-2">
                <i class="fas fa-trophy me-2"></i>My Badges
            </a>
        </div>
    </div>

    
</body>
</html>