<?php
session_start();
if (!isset($_SESSION["idEtudiant"])) {
    header("Location: ../auth/login_etudiant.php");
    exit;
}
?>


