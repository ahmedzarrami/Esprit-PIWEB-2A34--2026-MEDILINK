<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_destroy();
header('Location: /medilink_medicament/MediLink/views/rdv/front/loginPatient.php?logout=1');
exit;
?>
