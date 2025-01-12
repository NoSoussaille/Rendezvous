<?php
require_once "elements/header.php";

// Vérifie si l'utilisateur est un administrateur
if ($_SESSION['role'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}

// Gestion de la suppression d'un utilisateur
if (isset($_POST['supprimer'])) {
    $user_id = $_POST['user_id'];
    $stmt = $mysqli->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// Gestion de la recherche
$search = "";
if (!empty($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Nombre d'utilisateurs par page
$limit = 5;

// Page actuelle
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // S'assure que la page est au moins 1
$offset = ($page - 1) * $limit;

// Préparer la requête avec recherche et tri
$query = "
    SELECT id, nom, prenom, email, telephone 
    FROM utilisateurs 
    WHERE role != 'admin' 
";
if (!empty($search)) {
    $query .= "AND (nom LIKE ? OR prenom LIKE ? OR email LIKE ? OR telephone LIKE ?) ";
}
$query .= "ORDER BY nom ASC LIMIT ? OFFSET ?";

$stmt = $mysqli->prepare($query);

// Ajouter les paramètres de recherche et de pagination
if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bind_param("ssssii", $search_param, $search_param, $search_param, $search_param, $limit, $offset);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

// Compte total des utilisateurs pour la pagination
$count_query = "SELECT COUNT(*) AS total FROM utilisateurs WHERE role != 'admin'";
if (!empty($search)) {
    $count_query .= " AND (nom LIKE '%$search%' OR prenom LIKE '%$search%' OR email LIKE '%$search%' OR telephone LIKE '%$search%')";
}
$count_result = $mysqli->query($count_query);
$total_users = $count_result->fetch_assoc()['total'];

// Calcul du nombre total de pages
$total_pages = ceil($total_users / $limit);

?>

<div class="table-container">
    <h3 class="table-title">Gestion des clients</h3>
    
    <!-- Formulaire de recherche -->
    <form method="GET" action="gestionclients.php" style="margin-bottom: 20px; text-align:center;">
        <input type="text" name="search" placeholder="Rechercher par nom, prénom, email ou téléphone" value="<?php echo htmlspecialchars($search); ?>" style="padding: 8px; width: 250px; margin-bottom: 10px; border-radius: 20px;">
        <button type="submit" style="padding: 10px;">Rechercher</button>
    </form>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Nom et Prénom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php 
                            // Affiche le nom et le prénom concaténés
                            echo htmlspecialchars($row['nom'] . ' ' . $row['prenom']); 
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['telephone']); ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                            <button class="cta-button" type="submit" name="supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination" style="margin-top: 20px;">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" class="pagination-link">Précédent</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="pagination-link <?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" class="pagination-link">Suivant</a>
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