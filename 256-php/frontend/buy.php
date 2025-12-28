<?php
    require_once __DIR__ . '/../api/db.php';
    $gameId = $_GET["game_id"] ;
    $payment = $_GET["payment"];
    $userId = $_GET["user_id"];

    var_dump($gameId, $payment, $userId);

    $stmt = $db->prepare("INSERT INTO checkouts (date, user_id, payment_total, game_id) VALUES (NOW(), ?, ?,?)") ;
    $stmt->execute([$userId, $payment, $gameId]) ;

    header("Location: library.php") ;
?>