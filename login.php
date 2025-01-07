<?php
session_start();
require_once "db_connexion.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prépare la requête pour vérifier l'utilisateur
        $stmt = $mysqli->prepare("
            SELECT id, password 
            FROM utilisateurs 
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Vérifie le mot de passe
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: accueil.php"); // Redirection en cas de succès
                exit();
            } else {
                // Mot de passe invalide
                header("Location: connexion.php?error=1");
                exit();
            }
        } else {
            // Email non trouvé
            header("Location: connexion.php?error=1");
            exit();
        }

        $stmt->close();
    } else {
        // Champs manquants
        header("Location: connexion.php?error=1");
        exit();
    }
}

$mysqli->close();
?>