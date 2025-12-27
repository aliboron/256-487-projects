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
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'login' ? 'active' : ''; ?>" href="login.php"><i class="fa-solid fa-user"></i> Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>