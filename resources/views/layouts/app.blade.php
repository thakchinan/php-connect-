<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital HR System</title>
    <meta name="description" content="Modern Digital HR Management System for enterprise">
    <!-- Google Fonts: Inter & Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Small layout helper for navigation links inside page content */
        .nav-link.active {
            color: var(--primary-color);
            background: rgba(99, 102, 241, 0.08);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <script>
        // Apply theme immediately to body to prevent screen flash
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.body.classList.add('dark-mode');
            }
        })();
    </script>

    <!-- Background Glows -->
    <div class="glow-bg-1"></div>
    <div class="glow-bg-2"></div>

    <nav class="navbar">
        <div style="display: flex; align-items: center; gap: 2.5rem;">
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
                @endauth
            </ul>
        </div>

        <div class="navbar-actions" style="display: flex; align-items: center; gap: 1rem;">
            <!-- Theme Toggle Button -->
            <button id="theme-toggle" style="background: none; border: none; cursor: pointer; color: var(--text-main); display: flex; align-items: center; justify-content: center; padding: 0.5rem; border-radius: 50%; width: 38px; height: 38px; transition: background-color 0.2s;" title="สลับโหมดแสง/มืด">
                <!-- Moon SVG -->
                <svg id="theme-toggle-dark-icon" class="theme-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                <!-- Sun SVG -->
                <svg id="theme-toggle-light-icon" class="theme-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            </button>

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
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss toasts after 4 seconds
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

            // Theme Toggle Logic
            const themeToggleBtn = document.getElementById('theme-toggle');
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');

            function updateIcons() {
                if (document.body.classList.contains('dark-mode')) {
                    darkIcon.style.display = 'none';
                    lightIcon.style.display = 'block';
                } else {
                    darkIcon.style.display = 'block';
                    lightIcon.style.display = 'none';
                }
            }
            
            updateIcons();

            themeToggleBtn.addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                if (document.body.classList.contains('dark-mode')) {
                    localStorage.setItem('theme', 'dark');
                } else {
                    localStorage.setItem('theme', 'light');
                }
                updateIcons();
            });
        });
    </script>
</body>
</html>
