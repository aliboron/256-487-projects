<?php
// $activePage should be set before including this file
// Possible values: 'store', 'library', 'login'
if (!isset($activePage)) {
    $activePage = '';
}
?>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fa-solid fa-gamepad"></i> Game Store</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'store' ? 'active' : ''; ?>" href="index.php"><i class="fa-solid fa-store"></i> Store</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'library' ? 'active' : ''; ?>" href="library.php"><i class="fa-solid fa-book"></i> Library</a>
                </li>
                <!-- Login/User Section -->
                <li class="nav-item" id="navLoadingItem">
                    <a class="nav-link">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                    </a>
                </li>
                <li class="nav-item" id="loginNavItem" style="display: none;">
                    <a class="nav-link <?php echo $activePage === 'login' ? 'active' : ''; ?>" href="login.php">
                        <i class="fa-solid fa-user"></i> Login
                    </a>
                </li>
                <li class="nav-item dropdown" id="userNavItem" style="display: none;">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-user-circle"></i> <span id="navUsername">User</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="library.php"><i class="fa-solid fa-book"></i> My Library</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#" id="logoutBtn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    // Wait for DOM and jQuery to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // Load jQuery if not already loaded
        if (typeof jQuery === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://code.jquery.com/jquery-3.7.1.min.js';
            script.onload = initNavbar;
            document.head.appendChild(script);
        } else {
            initNavbar();
        }
    });

    function initNavbar() {
        jQuery(function($) {
            // Show loading indicator while checking auth
            $('#navLoadingItem').show();
            $('#loginNavItem').hide();
            $('#userNavItem').hide();

            // Check authentication status
            $.ajax({
                url: '/api/health',
                type: 'GET',
                xhrFields: {
                    withCredentials: true
                },
                success: function(response) {
                    console.log('Auth check response:', response);
                    $('#navLoadingItem').hide();
                    
                    if (response && response.success && response.data && response.data.user) {
                        // User is logged in
                        console.log('User is logged in:', response.data.user.username);
                        $('#loginNavItem').hide();
                        $('#userNavItem').show();
                        $('#navUsername').text(response.data.user.username);
                    } else {
                        console.log('User not logged in (no user data)');
                        $('#loginNavItem').show();
                        $('#userNavItem').hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Auth check error:', xhr.status, error);
                    $('#navLoadingItem').hide();
                    // User is not logged in, show login link
                    $('#loginNavItem').show();
                    $('#userNavItem').hide();
                }
            });

            // Handle logout
            $('#logoutBtn').on('click', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '/api/logout',
                    type: 'POST',
                    xhrFields: {
                        withCredentials: true
                    },
                    success: function() {
                        window.location.href = 'index.php';
                    },
                    error: function() {
                        // Even if logout fails, redirect to login
                        window.location.href = 'login.php';
                    }
                });
            });
        });
    }
</script>