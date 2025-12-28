<?php
    require_once __DIR__ . '/../api/db.php';
    $id = $_GET["checkoutId"] ;

    $stmt = $db->prepare("DELETE FROM checkouts WHERE id = ?") ;
    $stmt->execute([$id]) ;

    header("Location: library.php") ;
?>