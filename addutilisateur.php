<?php
require_once 'db_connexion.php';

// Traitement du formulaire d'ajout d'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $name = trim($_POST['name']);
    $firstname = trim($_POST['firstname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $role = 'utilisateur'; // Par défaut, les utilisateurs créés via ce formulaire ont le rôle "utilisateur"

    // Vérification des données obligatoires
    if (empty($name) || empty($firstname) || empty($email) || empty($phone) || empty($password)) {
        header("Location: creation.php?error=missing_fields");
        exit();
    }

    // Vérification de la validité de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: creation.php?error=invalid_email");
        exit();
    }

    // Vérification si l'email est déjà utilisé
    $stmt = $mysqli->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        header("Location: creation.php?error=email_taken");
        exit();
    }
    $stmt->close();

    // Hachage du mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insertion dans la base de données
    $stmt = $mysqli->prepare("
        INSERT INTO utilisateurs (nom, prenom, email, telephone, password, role) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssss", $name, $firstname, $email, $phone, $hashed_password, $role);

    if ($stmt->execute()) {
        header("Location: connexion.php?success=account_created");
        exit();
    } else {
        header("Location: creation.php?error=insert_failed");
        exit();
    }

    $stmt->close();
}

$mysqli->close();
?>