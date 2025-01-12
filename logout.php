<?php
session_start(); // Démarre la session si elle n'est pas déjà démarrée

// Détruire toutes les variables de session
$_SESSION = [];

// Détruire la session côté serveur
session_destroy();

// Rediriger l'utilisateur vers la page de connexion ou d'accueil
header("Location: connexion.php");
exit();
?>