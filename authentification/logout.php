<?php

session_start();

/* On détruit toutes les sessions */
session_destroy();

/* Redirection vers la page d'accueil ou login */
header("Location: ../index.php");
exit();

?>