<?php
require_once "elements/header.php";
// Vérifie si l'utilisateur est un administrateur
if ($_SESSION['role'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}

// Met à jour les rendez-vous passés pour les marquer comme "terminé"
$current_time = date('Y-m-d H:i:s');
$mysqli->query("
    UPDATE rendezvous 
    SET statut = 'terminé' 
    WHERE date_rdv < '$current_time' AND statut IN ('réservé', 'annulé')
");

// Gestion de l'annulation d'un rendez-vous
if (isset($_POST['annuler'])) {
    $rdv_id = $_POST['rendezvous_id'];
    $stmt = $mysqli->prepare("UPDATE rendezvous SET statut = 'annulé' WHERE id = ?");
    $stmt->bind_param("i", $rdv_id);
    $stmt->execute();
    $stmt->close();
}

// Récupère tous les rendez-vous
$query = "
    SELECT 
        r.id AS rendezvous_id, 
        u.nom AS utilisateur_nom, 
        u.prenom AS utilisateur_prenom, 
        u.telephone AS utilisateur_telephone, 
        s.nom_service AS service_nom, 
        r.date_rdv, 
        r.statut 
    FROM rendezvous r
    JOIN utilisateurs u ON r.utilisateur_id = u.id
    JOIN services s ON r.service_id = s.id
    WHERE r.statut != 'terminé' -- Exclut les rendez-vous terminés
    ORDER BY r.date_rdv ASC
";
$result = $mysqli->query($query);
?>
<div class="table-container">
    <h3 class="table-title">Gestion des rendez-vous</h3>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Nom et Prénom</th>
                <th>Téléphone</th>
                <th>Service</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php 
                            // Affiche le nom et le prénom concaténés
                            echo htmlspecialchars($row['utilisateur_nom'] . ' ' . $row['utilisateur_prenom']); 
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['utilisateur_telephone']); ?></td>
                    <td><?php echo htmlspecialchars($row['service_nom']); ?></td>
                    <td>
                        <?php 
                            // Formater la date en JJ/MM/AAAA
                            $date_rdv = date('d/m/Y H:i', strtotime($row['date_rdv']));
                            echo htmlspecialchars($date_rdv); 
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['statut']); ?></td>
                    <td>
                        <?php if ($row['statut'] !== 'annulé'): ?>
                            <form method="POST" action="">
                                <input type="hidden" name="rendezvous_id" value="<?php echo $row['rendezvous_id']; ?>">
                                <button class="cta-button" type="submit" name="annuler">Annuler</button>
                            </form>
                        <?php else: ?>
                            <span class="annule">Annulé</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
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