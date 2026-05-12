<?php
require_once __DIR__ . '/../../../../../config/session.php';
medilink_logout();
header('Location: /files40/modules/utilisateur/index.php?page=login');
exit;
