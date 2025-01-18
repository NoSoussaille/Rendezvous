<?php
require_once 'elements/header.php'; // Inclusion de l'entête
require 'vendor/autoload.php'; // Chargement des dépendances MongoDB

// Vérification si l'utilisateur est administrateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Accès refusé.');
}

// Connexion à MongoDB
$client = new MongoDB\Client("mongodb://127.0.0.1:27017");
$db = $client->rendezvousDB;
$collection = $db->rendezvous;

// Bouton de synchronisation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sync'])) {

    // Récupérer les rendez-vous SQL
    $result = $mysqli->query("
        SELECT 
            r.id, 
            r.date_rdv AS date, 
            s.nom_service AS service 
        FROM 
            rendezvous r
        JOIN 
            services s 
        ON 
            r.service_id = s.id
    ");
    $sqlData = [];
    while ($row = $result->fetch_assoc()) {
        $sqlData[] = [
            'id' => $row['id'],
            'date' => $row['date'], 
            'service' => $row['service'],
        ];
    }

    // Insérer ou mettre à jour dans MongoDB
    foreach ($sqlData as $data) {
        $collection->updateOne(
            ['id' => $data['id']],
            ['$set' => $data],
            ['upsert' => true]
        );
    }

    $syncMessage = "Synchronisation terminée.";
}

// Statistiques MongoDB
$totalRendezvous = $collection->countDocuments();

// Répartition par date (pagination)
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10; // Nombre de résultats par page
$skip = ($page - 1) * $limit;

$rendezvousParDate = $collection->aggregate([
    ['$group' => ['_id' => ['$substr' => ['$date', 0, 10]], 'nombre' => ['$sum' => 1]]],
    ['$sort' => ['_id' => 1]],
    ['$skip' => $skip],
    ['$limit' => $limit]
]);

$totalDates = $collection->aggregate([
    ['$group' => ['_id' => ['$substr' => ['$date', 0, 10]]]],
    ['$count' => 'total']
]);
$totalDates = iterator_to_array($totalDates);
$totalPages = isset($totalDates[0]['total']) ? ceil($totalDates[0]['total'] / $limit) : 1;

// Répartition par service
$rendezvousParService = $collection->aggregate([
    ['$group' => ['_id' => '$service', 'nombre' => ['$sum' => 1]]],
    ['$sort' => ['nombre' => -1]]
]);

// Années disponibles pour les rendez-vous
$years = $collection->aggregate([
    ['$group' => ['_id' => ['$substr' => ['$date', 0, 4]]]],
    ['$sort' => ['_id' => 1]]
]);
$years = array_map(fn($entry) => $entry['_id'], iterator_to_array($years));
?>

<div class="table-container">
    <h3 class="table-title">Statistiques des Rendez-vous</h3>

    <!-- Bouton de synchronisation -->
    <form method="POST" action="">
        <button type="submit" name="sync" class="sync-button">Synchroniser les données</button>
    </form>

    <?php if (isset($syncMessage)): ?>
        <p id="sync-message" class="sync-message"><?php echo htmlspecialchars($syncMessage); ?></p>
    <?php endif; ?>

    <!-- Nombre total de rendez-vous -->
    <div class="stat-total">
        <h4>Total des rendez-vous</h4>
        <p><strong><?php echo $totalRendezvous; ?></strong> rendez-vous ont été pris.</p>
    </div>

    <!-- Sélecteur d'année -->
    <h4 class="table-subtitle">Rendez-vous par année</h4>
    <div class="tabs">
        <?php foreach ($years as $year): ?>
            <a href="?year=<?php echo $year; ?>" class="tab">
                <?php echo $year; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Répartition par date avec pagination -->
    <h4 class="table-subtitle">Répartition par date</h4>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Nombre de rendez-vous</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rendezvousParDate as $entry): ?>
                <tr>
                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($entry['_id']))); ?></td>
                    <td><?php echo htmlspecialchars($entry['nombre'] ?? '0'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="pagination-link <?php echo $i === $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>

    <!-- Répartition par service -->
    <h4 class="table-subtitle">Répartition par service</h4>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Service</th>
                <th>Nombre de rendez-vous</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rendezvousParService as $entry): ?>
                <tr>
                    <td><?php echo htmlspecialchars($entry['_id'] ?? 'Non défini'); ?></td>
                    <td><?php echo htmlspecialchars($entry['nombre'] ?? '0'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const burgerMenu = document.getElementById('burger-menu');
        const navMenu = document.querySelector('.navigation-menu');

        burgerMenu.addEventListener('click', () => {
            navMenu.classList.toggle('active'); // Affiche/Cache le menu
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const syncMessage = document.getElementById('sync-message');
        if (syncMessage) {
            // Masquer le message après 15 secondes (15 000 millisecondes)
            setTimeout(() => {
                syncMessage.style.transition = "opacity 1s";
                syncMessage.style.opacity = "0";
                // Supprime complètement après la transition
                setTimeout(() => syncMessage.remove(), 1000);
            }, 15000);
        }
    });
</script>
</body>
</html>
