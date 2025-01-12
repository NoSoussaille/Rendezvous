<?php
session_start();
require_once "db_connexion.php";
$role = $_SESSION['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Rendez-Vous</title>
</head>
<body>
<header class="header-container">
    <div class="header-content">
        <h1 class="header-title">
            <a href="accueil.php" class="header-link">Rendez-vous</a>
        </h1>
        <button class="menu-toggle" id="burger-menu">&#9776;</button>
        <nav class="navigation-menu">
            <ul class="menu-list">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'utilisateur'): ?>
                    <li class="menu-item"><a href="utilisateurRDV.php" class="menu-link">Mes rendez-vous</a></li>
                    <li class="menu-item"><a href="moncompte.php" class="menu-link">Mon compte</a></li>
                    <li class="menu-item"><a href="logout.php" class="menu-link">Déconnexion</a></li>
                <?php endif; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li class="menu-item"><a href="gestionrdv.php" class="menu-link">Gestion rendez-vous</a></li>
                    <li class="menu-item"><a href="gestionclients.php" class="menu-link">Gestion clients</a></li>
                    <li class="menu-item"><a href="logout.php" class="menu-link">Déconnexion</a></li>
                <?php endif; ?>
                <?php if (!isset($_SESSION['role'])): ?>
                    <li class="menu-item"><a href="connexion.php" class="menu-link">Connexion</a></li>
                <?php endif; ?>
                
            </ul>
        </nav>
    </div>
</header>