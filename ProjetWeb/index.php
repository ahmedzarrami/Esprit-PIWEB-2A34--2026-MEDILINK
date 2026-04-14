<?php
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'home':
        require "views/front/home.php";
        break;
    case 'admin':
        require "views/admin/admin.php";
        break;
}
?>