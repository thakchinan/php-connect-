<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital HR System</title>
    <meta name="description" content="Modern Digital HR Management System for enterprise">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Sarabun', 'Inter', sans-serif;
        }
        /* Alert Banner Styles */
        .alert-container {
            max-width: 1200px;
            margin: 1rem auto 0;
            padding: 0 1rem;
        }
        .alert {
            padding: 1rem 1.25rem;
            border-radius: var(--radius-md);
            margin-bottom: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: fadeIn 0.3s ease-out forwards;
        }
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #047857;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #b91c1c;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .alert-close {
            cursor: pointer;
            background: none;
            border: none;
            color: inherit;
            font-weight: bold;
            font-size: 1.2rem;
            line-height: 1;
        }
        /* Active nav link highlight */
        .nav-link.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
        }
        /* Glass card enhancement */
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        }
        /* Grid and Forms utility */
        .form-group {
            margin-bottom: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
        }
        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-main);
        }
        .form-control {
            padding: 0.625rem 0.875rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
            background-color: rgba(255, 255, 255, 0.8);
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
        }
        .badge-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #065f46;
        }
        .badge-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: #92400e;
        }
        .badge-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #991b1b;
        }
        /* Footer */
        .footer {
            margin-top: auto;
            padding: 2rem 0;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.875rem;
            border-top: 1px solid var(--border-color);
        }
    </style>
</head>
<body>
    <!-- Background Glows -->
    <div class="glow-bg-1"></div>
    <div class="glow-bg-2"></div>

    <nav class="navbar">
        <div class="brand">
            <a href="{{ route('welcome') }}" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: inherit;">
                <div class="brand-icon bg-gradient-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                Digital HR
            </a>
        </div>
        
        <ul class="navbar-nav">
            @auth
                <li><a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
                <li><a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">Employees</a></li>
                <li><a href="{{ route('leaves.index') }}" class="nav-link {{ request()->routeIs('leaves.*') ? 'active' : '' }}">Leave & Time</a></li>
                <li><a href="{{ route('performance.index') }}" class="nav-link {{ request()->routeIs('performance.*') ? 'active' : '' }}">Performance</a></li>
            @else
                <li><a href="{{ route('welcome') }}" class="nav-link {{ request()->routeIs('welcome') ? 'active' : '' }}">หน้าแรก</a></li>
            @endauth
        </ul>

        <div class="navbar-actions">
            @auth
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span style="font-size: 0.875rem; font-weight: 500; color: var(--text-main);">สวัสดี, <strong>{{ Auth::user()->name }}</strong></span>
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="padding: 0.375rem 1rem;">Sign Out</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">Sign In</a>
            @endauth
        </div>
    </nav>

    <div class="main-content">
        @yield('content')
        
        <footer class="footer">
            <div class="container">
                <p>&copy; 2026 Digital HR System - SCG Cooperative Education Project. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <!-- Toast Notifications -->
    <div class="toast-container">
        @if(session('success'))
            <div class="toast toast-success" id="success-toast">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <span style="font-weight: 500;">{{ session('success') }}</span>
                </div>
                <button class="toast-close" onclick="document.getElementById('success-toast').style.display='none'">&times;</button>
            </div>
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="toast toast-danger" id="error-toast-{{ $loop->index }}">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                        <span style="font-weight: 500;">{{ $error }}</span>
                    </div>
                    <button class="toast-close" onclick="document.getElementById('error-toast-{{ $loop->index }}').style.display='none'">&times;</button>
                </div>
            @endforeach
        @endif
    </div>

    <script>
        // Auto-dismiss toasts after 4 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.toast');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(10px) scale(0.95)';
                    setTimeout(() => {
                        toast.remove();
                    }, 500);
                }, 4000);
            });
        });
    </script>
</body>
</html>
