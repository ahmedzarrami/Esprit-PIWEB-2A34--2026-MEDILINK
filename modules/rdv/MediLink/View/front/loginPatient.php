<?php
/**
 * Login patient legacy : redirige vers le login MediLink unifie.
 */
require_once __DIR__ . '/../../../../../config/session.php';
header('Location: /files40/modules/utilisateur/index.php?page=login');
exit;
