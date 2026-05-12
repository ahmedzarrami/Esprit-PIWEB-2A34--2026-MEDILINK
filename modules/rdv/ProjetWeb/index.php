<?php
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'home':
        require "View/front/home.php";
        break;
    case 'patient':
        require "View/front/homePatient.php";
        break;
    case 'medecin':
        require "View/front/home.php"; // Les médecins utilisent la même page home pour l'instant
        break;
    case 'admin':
        require "View/admin/admin.php";
        break;
}
?>