@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Welcome, {{ Auth::user()->name }}! 👋</h1>
    
    @php
        $stats = Auth::user()->getBadgeStats();
        $categories = \App\Models\BadgeCategory::withCount(['badges'])->get();
        $userBadges = Auth::user()->badges()->orderBy('user_badges.created_at', 'desc')->take(6)->get();
    @endphp
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Badges</h5>
                    <h2 class="card-text">{{ $stats['total'] }}</h2>
                    <p class="card-text">Out of 32 badges</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Approved</h5>
                    <h2 class="card-text">{{ $stats['approved'] }}</h2>
                    <p class="card-text">Completed badges</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Pending</h5>
                    <h2 class="card-text">{{ $stats['pending'] }}</h2>
                    <p class="card-text">Under review</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Total XP</h5>
                    <h2 class="card-text">{{ $stats['xp_total'] }}</h2>
                    <p class="card-text">Experience Points</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Badges -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Recent Badges</h5>
        </div>
        <div class="card-body">
            @if($userBadges->count() > 0)
            <div class="row">
                @foreach($userBadges as $badge)
                <div class="col-md-4 mb-3">
                    <div class="card h-100" style="border-top: 5px solid {{ $badge->color }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="card-title">{{ $badge->name }}</h6>
                                <span class="badge bg-{{ $badge->pivot->status == 'approved' ? 'success' : 'warning' }}">
                                    {{ ucfirst($badge->pivot->status) }}
                                </span>
                            </div>
                            <p class="card-text text-muted small">{{ $badge->category->name }}</p>
                            <p class="card-text small">
                                <i class="fas fa-calendar"></i> 
                                {{ $badge->pivot->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-4">
                <i class="fas fa-award fa-3x text-muted mb-3"></i>
                <h5>No badges yet</h5>
                <p>Start earning badges by completing HCI projects</p>
                <a href="{{ route('badges.index') }}" class="btn btn-primary">Browse All Badges</a>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-search fa-2x text-primary mb-3"></i>
                    <h5>Browse Badges</h5>
                    <p>Explore all available HCI badges</p>
                    <a href="{{ route('badges.index') }}" class="btn btn-outline-primary">View All</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-user fa-2x text-success mb-3"></i>
                    <h5>My Profile</h5>
                    <p>View your badge collection</p>
                    <a href="{{ route('badges.my') }}" class="btn btn-outline-success">My Badges</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection