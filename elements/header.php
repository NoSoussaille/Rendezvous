<?php
session_start();
require_once "db_connexion.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Rendez-Vous</title>
</head>
<body>
    <header>
        <h1>Rendez vous</h1>
        <ul>
            <li>Mes rendez vous</li>
            <li>Mon compte</li>
            <li>Déconnexion</li>
        </ul>
    </header>
