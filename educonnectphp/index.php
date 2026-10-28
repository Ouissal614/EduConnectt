<?php
// point d'entrée du système EduConnect

session_start();

// si déjà connecté → aller au dashboard
if(isset($_SESSION['parent'])) {
    header("Location: pages/dashboard.php");
    exit();
}

// sinon → aller au login
header("Location: authentification/login.php");
exit();
?>