<?php $activePage = 'login'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="./vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/login.css">
    <title>CTIS256 - Digital Game Market - Welcome!</title>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4 fw-bold">Welcome to Game Store</h2>

                        <!-- Nav tabs -->
                        <ul class="nav nav-pills nav-justified mb-4" id="authTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="login-tab" data-bs-toggle="tab"
                                    data-bs-target="#login" type="button" role="tab">
                                    <i class="fa-solid fa-right-to-bracket"></i> Login
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="register-tab" data-bs-toggle="tab"
                                    data-bs-target="#register" type="button" role="tab">
                                    <i class="fa-solid fa-user-plus"></i> Register
                                </button>
                            </li>
                        </ul>

                        <!-- Tab content -->
                        <div class="tab-content">
                            <!-- Login Form -->
                            <div class="tab-pane fade show active" id="login" role="tabpanel">
                                <form id="loginForm">
                                    <div class="mb-3">
                                        <label for="login-username" class="form-label"><i class="fa-solid fa-user"></i> Username</label>
                                        <input type="text" class="form-control" id="login-username"
                                            name="username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="login-password" class="form-label"><i class="fa-solid fa-lock"></i> Password</label>
                                        <input type="password" class="form-control" id="login-password"
                                            name="password" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 mb-3"><i class="fa-solid fa-right-to-bracket"></i> Login</button>
                                    <button type="button" class="btn btn-outline-secondary w-100" id="guestBtn">
                                        <i class="fa-solid fa-user-secret"></i> Continue as Guest
                                    </button>
                                </form>
                            </div>

                            <!-- Register Form -->
                            <div class="tab-pane fade" id="register" role="tabpanel">
                                <form id="registerForm">
                                    <div class="mb-3">
                                        <label for="register-username" class="form-label"><i class="fa-solid fa-user"></i> Username</label>
                                        <input type="text" class="form-control" id="register-username"
                                            name="username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="register-email" class="form-label"><i class="fa-solid fa-envelope"></i> Email</label>
                                        <input type="email" class="form-control" id="register-email"
                                            name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="register-phone" class="form-label"><i class="fa-solid fa-phone"></i> Phone</label>
                                        <input type="tel" class="form-control" id="register-phone"
                                            name="phone" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fa-solid fa-venus-mars"></i> Gender</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="gender"
                                                    id="gender-male" value="male" required>
                                                <label class="form-check-label" for="gender-male">Male</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="gender"
                                                    id="gender-female" value="female">
                                                <label class="form-check-label" for="gender-female">Female</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="gender"
                                                    id="gender-other" value="other">
                                                <label class="form-check-label" for="gender-other">Other</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="register-birth-date" class="form-label"><i class="fa-solid fa-cake-candles"></i> Birth Date</label>
                                        <input type="date" class="form-control" id="register-birth-date"
                                            name="birth_date" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="register-password" class="form-label"><i class="fa-solid fa-lock"></i> Password</label>
                                        <input type="password" class="form-control" id="register-password"
                                            name="password" required minlength="6">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fa-solid fa-id-card"></i> Account Type</label>
                                        <div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type"
                                                    id="type-user" value="user" required>
                                                <label class="form-check-label" for="type-user">
                                                    <strong>User</strong> - Play and purchase games
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type"
                                                    id="type-developer" value="game_developer">
                                                <label class="form-check-label" for="type-developer">
                                                    <strong>Game Developer</strong> - Publish and sell games
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100"><i class="fa-solid fa-user-plus"></i> Register</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center text-muted mt-3">
                    <small>&copy; 2025 CTIS256 - Game Store. All rights reserved.</small>
                </p>
            </div>
        </div>
    </div>

    <script src="./vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>