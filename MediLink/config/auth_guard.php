<?php
/**
 * auth_guard.php — Protection d'accès back-office
 * Inclure en haut de chaque page back-office qui nécessite une authentification admin.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_guardRole = strtolower($_SESSION['user_role'] ?? '');

if (empty($_SESSION['user_id']) || $_guardRole !== 'administrateur') {
    header('Location: /medilink_medicament/MediLink/index.php?page=login&redirect=back');
    exit;
}
