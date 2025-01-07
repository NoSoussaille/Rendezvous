<?php
session_start();

// Détruit toutes les données de session
session_unset();
session_destroy();

// Redirige vers la page de connexion
header("Location: connexion.php");
exit();