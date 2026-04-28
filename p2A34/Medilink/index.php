<?php
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'home':
        require "Views/front/home.php";
        break;
    case 'patient':
        require "Views/front/homePatient.php";
        break;
    case 'medecin':
        require "Views/front/home.php"; // Les médecins utilisent la même page home pour l'instant
        break;
    case 'admin':
        require "Views/admin/admin.php";
        break;
}
?>