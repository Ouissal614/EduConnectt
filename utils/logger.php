<?php

function logLogin($pdo, $type, $user_id, $login, $status) {

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    $sql = "INSERT INTO login_logs (user_type, user_id, login, status, ip_address)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$type, $user_id, $login, $status, $ip]);
}