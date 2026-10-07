<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MonitorSuhu')</title>
    
    <!-- Inline script runs BEFORE rendering to eliminate page load flicker -->
    <script>
        if (localStorage.getItem('sidebar_open') === 'true') {
            document.documentElement.classList.add('sidebar-open');
        }
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --sidebar-width: 260px;
        }

        body { 
            background-color: #f8f9fa; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            overflow-x: hidden;
        }

        /* Fixed Sidebar Styling */
        .offcanvas-sidebar { 
            width: var(--sidebar-width) !important; 
            height: 100vh;
            position: fixed;
            top: 0;
            left: calc(-1 * var(--sidebar-width));
            background-color: #1e1e2d; 
            color: #a2a3b7; 
            z-index: 1040;
            transition: left 0.3s ease-in-out;
            overflow-y: auto;
        }
        
        .offcanvas-sidebar .nav-link { 
            color: #a2a3b7; 
            border-radius: 8px; 
            margin-bottom: 4px; 
            padding: 10px 15px; 
        }

        .offcanvas-sidebar .nav-link:hover, 
        .offcanvas-sidebar .nav-link.active { 
            background-color: #2b2b40; 
            color: #ffffff; 
        }

        /* Smooth Content Push Setup */
        .page-wrapper {
            transition: margin-left 0.3s ease-in-out;
            margin-left: 0;
            min-height: 100vh;
        }

        /* Open State: Pushes content right instead of overlaying */
        html.sidebar-open .offcanvas-sidebar {
            left: 0 !important;
        }

        html.sidebar-open .page-wrapper {
            margin-left: var(--sidebar-width);
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Push Navigation Sidebar -->
    <div class="offcanvas-sidebar p-3" id="sidebarMenu">
        <div class="d-flex justify-content-between align-items-center pb-3 border-bottom border-secondary">
            <h5 class="text-white fw-bold m-0">
                <i class="fa-solid fa-server text-primary me-2"></i> Navigation
            </h5>
            <button type="button" class="btn-close btn-close-white" id="closeSidebarBtn"></button>
        </div>
        <div class="d-flex flex-column justify-content-between px-0 pt-3">
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line me-2"></i> Dashboard
                    </a>
                </li>
                
                <li class="nav-item mt-2">
                    <a href="#serverListSubmenu" class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse">
                        <span><i class="fa-solid fa-server me-2"></i> Server List</span>
                        <i class="fa-solid fa-chevron-down small"></i>
                    </a>
                    <div class="collapse show ps-3" id="serverListSubmenu">
                        <ul class="nav flex-column mt-1 gap-1">
                            <li class="nav-item">
                                <a href="{{ route('rack1') }}" class="nav-link py-2 {{ request()->routeIs('rack1') ? 'active' : '' }}">
                                    <i class="fa-solid fa-hard-drive me-2"></i> Server Rack 1
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('rack2') }}" class="nav-link py-2 {{ request()->routeIs('rack2') ? 'active' : '' }}">
                                    <i class="fa-solid fa-hard-drive me-2"></i> Server Rack 2
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Wrapper (Shrinks/Pushes Screen) -->
    <div class="page-wrapper">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4">
            <div class="container-fluid p-0">
                <div class="d-flex align-items-center">
                    <button class="btn btn-light border me-3 shadow-sm" type="button" id="toggleSidebarBtn">
                        <i class="fa-solid fa-bars fs-5"></i>
                    </button>
                    <a class="navbar-brand fw-bold text-dark m-0" href="{{ url('/') }}">
                        <i class="fa-solid fa-server text-primary me-2"></i>MonitorSuhu
                    </a>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="d-flex">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-2">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="container-fluid px-4 py-4">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggleSidebarBtn');
            const closeBtn = document.getElementById('closeSidebarBtn');

            function toggleSidebar() {
                const isOpen = document.documentElement.classList.toggle('sidebar-open');
                localStorage.setItem('sidebar_open', isOpen ? 'true' : 'false');
                
                // Triggers chart auto-resizing smoothly when sidebar opens/closes
                setTimeout(() => {
                    window.dispatchEvent(new Event('resize'));
                }, 300);
            }

            if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
            if (closeBtn) closeBtn.addEventListener('click', toggleSidebar);
        });
    </script>

    @stack('scripts')
</body>
</html>