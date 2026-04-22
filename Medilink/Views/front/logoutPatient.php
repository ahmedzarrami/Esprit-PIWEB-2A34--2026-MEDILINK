<?php
session_start();
session_destroy();
header('Location: loginPatient.php?logout=1');
exit;
?>
