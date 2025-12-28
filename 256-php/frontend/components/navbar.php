<?php
// Start session to check user authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// $activePage should be set before including this file
// Possible values: 'store', 'library', 'login', 'developer', 'admin'
if (!isset($activePage)) {
    $activePage = '';
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['user_data']);
$username = $isLoggedIn ? $_SESSION['user_data']['username'] : '';
$userType = $isLoggedIn ? $_SESSION['user_data']['type'] : '';
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
                <!-- Developer Dashboard (shown only for game developers) -->
                <?php if ($isLoggedIn && $userType === 'game_developer'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activePage === 'developer' ? 'active' : ''; ?>" href="game_developer.php">
                            <i class="fa-solid fa-code"></i> Developer Dashboard
                        </a>
                    </li>
                <?php endif; ?>
                <!-- Admin Dashboard (shown only for admins) -->
                <?php if ($isLoggedIn && $userType === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activePage === 'admin' ? 'active' : ''; ?>" href="admin.php">
                            <i class="fa-solid fa-shield-halved"></i> Admin Dashboard
                        </a>
                    </li>
                <?php endif; ?>
                <!-- Login/User Section -->
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-circle"></i> <?php echo htmlspecialchars($username); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="library.php"><i class="fa-solid fa-book"></i> My Library</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#" id="logoutBtn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activePage === 'login' ? 'active' : ''; ?>" href="login.php">
                            <i class="fa-solid fa-user"></i> Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        $('#logoutBtn').on('click', function(e) {
            e.preventDefault();

            $.ajax({
                url: '/api/logout',
                type: 'POST',
                xhrFields: {
                    withCredentials: true
                },
                success: function() {
                    localStorage.removeItem('userId');
                    localStorage.removeItem('username');
                    localStorage.removeItem('userType');
                    window.location.href = 'index.php';
                },
                error: function() {
                    localStorage.removeItem('userId');
                    localStorage.removeItem('username');
                    localStorage.removeItem('userType');
                    window.location.href = 'login.php';
                }
            });
        });
    });
</script>