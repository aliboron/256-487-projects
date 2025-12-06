<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Document</title>
</head>
<body>
    <form action="login.php" method="POST">
        <div class="input-field">

            <label for="username">Username:</label>
            <input name="username"type="text">
        </div>
        <div class="input-field">    
            <label for="password">Password:w</label>
            <input type="password" name="password">
        </div>
        <div class="input-field">

            <button type="submit">Login</button>
        </div>
    </form>
    <?php
        session_start();
        $hashedPassword=password_hash($password,PASSWORD_BCRYPT);
    ?>
</body>
</html>