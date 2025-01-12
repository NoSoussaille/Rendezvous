<?php
require_once "elements/header.php";
require_once "db_connexion.php";

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

// ID de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

// Récupère les informations de l'utilisateur
$query = "
    SELECT nom, prenom, email, telephone 
    FROM utilisateurs 
    WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Gestion de la suppression de compte
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    // Supprime l'utilisateur et ses rendez-vous associés
    $delete_rdv_query = "DELETE FROM rendezvous WHERE utilisateur_id = ?";
    $stmt = $mysqli->prepare($delete_rdv_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $delete_user_query = "DELETE FROM utilisateurs WHERE id = ?";
    $stmt = $mysqli->prepare($delete_user_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Déconnecte l'utilisateur
    session_destroy();

    // Redirige vers la page de connexion
    header("Location: connexion.php?account_deleted=1");
    exit();
}
?>

<div class="account-container">
    <div class="account-card">
        <h2 class="account-title">Mon Compte</h2>

        <?php if ($user): ?>
            <div class="account-info">
                <p><strong>Nom :</strong> <?php echo htmlspecialchars($user['nom']); ?></p>
                <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user['prenom']); ?></p>
                <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($user['telephone']); ?></p>
            </div>

            <form method="POST" class="account-delete-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.');">
                <button type="submit" name="delete_account" class="delete-button">Supprimer mon compte</button>
            </form>
        <?php else: ?>
            <p class="account-error">Erreur : Impossible de récupérer les informations de votre compte.</p>
        <?php endif; ?>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const burgerMenu = document.getElementById('burger-menu');
        const navMenu = document.querySelector('.navigation-menu');

        burgerMenu.addEventListener('click', () => {
            navMenu.classList.toggle('active'); // Affiche/Cache le menu
        });
    });
</script>
</body>
</html>