<?php
require_once "elements/header.php";
require_once "db_connexion.php";

// Vérifie si l'utilisateur est connecté et n'est pas un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') {
    header("Location: connexion.php");
    exit();
}

// ID de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

// Gestion de la pagination
$limit = 5; // Nombre de rendez-vous par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // S'assure que la page est au moins 1
$offset = ($page - 1) * $limit;

// Récupération des rendez-vous de l'utilisateur
$query = "
    SELECT 
        r.date_rdv, 
        s.nom_service AS service_nom, 
        r.statut 
    FROM rendezvous r
    JOIN services s ON r.service_id = s.id
    WHERE r.utilisateur_id = ?
    ORDER BY r.date_rdv DESC
    LIMIT ? OFFSET ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("iii", $user_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Compte total des rendez-vous pour la pagination
$count_query = "
    SELECT COUNT(*) AS total 
    FROM rendezvous 
    WHERE utilisateur_id = ?";
$count_stmt = $mysqli->prepare($count_query);
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_rdv = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rdv / $limit);

?>

<div class="table-container">
    <h3 class="table-title">Mes Rendez-vous</h3>

    <?php if ($result->num_rows > 0): ?>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Service</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php 
                                // Afficher la date au format JJ/MM/AAAA HH:mm
                                $date_rdv = date('d/m/Y H:i', strtotime($row['date_rdv']));
                                echo htmlspecialchars($date_rdv); 
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['service_nom']); ?></td>
                        <td><?php echo htmlspecialchars($row['statut']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination" style="margin-top: 20px;">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="pagination-link">Précédent</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="pagination-link <?php echo $i === $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="pagination-link">Suivant</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p class="no-data">Aucun rendez-vous trouvé.</p>
    <?php endif; ?>
</div>

<?php
// Fermer les requêtes
$stmt->close();
$count_stmt->close();
?>
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