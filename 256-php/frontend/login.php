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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="./vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const redirect = urlParams.get('redirect');
            const gameId = urlParams.get('game_id');

            // Handle login form submission
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                const username = $('#login-username').val();
                const password = $('#login-password').val();
                const submitBtn = $(this).find('button[type="submit"]');

                // Disable button and show loading state
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fa-solid fa-spinner fa-spin"></i> Logging in...');

                $.ajax({
                    url: '../api/login',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        username: username,
                        password: password
                    }),
                    xhrFields: {
                        withCredentials: true // Include cookies
                    },
                    success: function(response) {
                        console.log('Login successful:', response);

                        // Handle redirect
                        if (redirect === 'checkout' && gameId) {
                            window.location.href = 'checkout.php?game_id=' + gameId;
                        } else if (redirect) {
                            window.location.href = redirect + '.php';
                        } else {
                            // Redirect based on user type
                            if (response.data && response.data.user) {
                                if (response.data.user.type === 'game_developer') {
                                    window.location.href = 'game_developer.php';
                                } else if (response.data.user.type === 'admin') {
                                    window.location.href = 'admin.php';
                                } else {
                                    window.location.href = 'index.php';
                                }
                            } else {
                                window.location.href = 'index.php';
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Login error:', error);
                        let errorMsg = 'Login failed. Please try again.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.status === 401) {
                            errorMsg = 'Invalid username or password.';
                        }

                        alert(errorMsg);

                        // Re-enable button
                        submitBtn.prop('disabled', false);
                        submitBtn.html('<i class="fa-solid fa-right-to-bracket"></i> Login');
                    }
                });
            });

            // Handle register form submission
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    username: $('#register-username').val(),
                    email: $('#register-email').val(),
                    phone: $('#register-phone').val(),
                    gender: $('input[name="gender"]:checked').val(),
                    birth_date: $('#register-birth-date').val(),
                    password: $('#register-password').val(),
                    type: $('input[name="type"]:checked').val()
                };

                const submitBtn = $(this).find('button[type="submit"]');

                // Disable button and show loading state
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fa-solid fa-spinner fa-spin"></i> Registering...');

                $.ajax({
                    url: '../api/register',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    xhrFields: {
                        withCredentials: true // Include cookies
                    },
                    success: function(response) {
                        console.log('Registration successful:', response);
                        alert('Registration successful! You can now login.');

                        // Switch to login tab
                        $('#login-tab').tab('show');

                        // Clear form
                        $('#registerForm')[0].reset();

                        // Re-enable button
                        submitBtn.prop('disabled', false);
                        submitBtn.html('<i class="fa-solid fa-user-plus"></i> Register');
                    },
                    error: function(xhr, status, error) {
                        console.error('Registration error:', error);
                        let errorMsg = 'Registration failed. Please try again.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.status === 409) {
                            errorMsg = 'Username already exists. Please choose another.';
                        }

                        alert(errorMsg);

                        // Re-enable button
                        submitBtn.prop('disabled', false);
                        submitBtn.html('<i class="fa-solid fa-user-plus"></i> Register');
                    }
                });
            });

            // Handle guest button
            $('#guestBtn').on('click', function() {
                window.location.href = 'index.php';
            });
        });
    </script>
</body>

</html>