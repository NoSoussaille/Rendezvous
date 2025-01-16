<?php
require_once "elements/header.php";
require_once "db_connexion.php";

$message = ""; // Variable pour afficher les messages

// Vérifie le rôle de l'utilisateur
$role = $_SESSION['role'] ?? null;

// Gestion du formulaire pour les utilisateurs
if ($role === 'utilisateur' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? null;
    $time = $_POST['time'] ?? null;
    $service = $_POST['service'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;

    if ($date && $time && $service && $user_id) {
        $dateTime = $date . ' ' . explode(' - ', $time)[0] . ':00';

        $query = "SELECT id FROM rendezvous WHERE date_rdv = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $dateTime);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "<div class='message-error'>Ce créneau est déjà réservé.</div>";
        } else {
            $insert = "INSERT INTO rendezvous (utilisateur_id, date_rdv, service_id, statut) VALUES (?, ?, ?, 'réservé')";
            $stmt = $mysqli->prepare($insert);
            $stmt->bind_param("isi", $user_id, $dateTime, $service);

            if ($stmt->execute()) {
                $formattedDate = date('d/m/Y', strtotime($date));
                $message = "
                    <div class='message-success'>
                        Rendez-vous réservé avec succès pour le <strong>$formattedDate</strong> à <strong>$time</strong>.
                    </div>
                ";
            } else {
                $message = "<div class='message-error'>Erreur lors de la réservation.</div>";
            }
        }
    } else {
        $message = "<div class='message-error'>Tous les champs sont obligatoires.</div>";
    }
}

// Gestion de l'affichage des rendez-vous pour les administrateurs
$rendezvous_today = [];
if ($role === 'admin') {
    $today = date('Y-m-d');
    $query = "
        SELECT 
            u.nom AS utilisateur_nom, 
            u.prenom AS utilisateur_prenom, 
            s.nom_service AS service_nom, 
            r.date_rdv 
        FROM rendezvous r
        JOIN utilisateurs u ON r.utilisateur_id = u.id
        JOIN services s ON r.service_id = s.id
        WHERE DATE(r.date_rdv) = ?
        ORDER BY r.date_rdv ASC
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $rendezvous_today = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="container">
    <?php if ($role === 'utilisateur'): ?>
        <!-- Formulaire de prise de rendez-vous pour les utilisateurs -->
        <div class="title1">
            <h1 class="title2">Prenez rendez-vous</h1>
        </div>
        <form action="" method="post" class="appointment-form">
            <div class="form-group">
                <label for="date" class="form-label">Date :</label>
                <input type="date" id="date" name="date" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="time" class="form-label">Horaire :</label>
                <select id="time" name="time" class="form-input" required>
                    <option value="">Sélectionnez une date d'abord</option>
                </select>
            </div>
            <div class="form-group">
                <label for="service" class="form-label">Service :</label>
                <select id="service" name="service" class="form-input" required>
                    <option value="1">Médecine douce</option>
                    <option value="2">Thérapie</option>
                    <option value="3">Suivi diététique</option>
                    <option value="4">Entretien organe féminin</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="form-button">Valider</button>
            </div>
        </form>
        <?php if (!empty($message)): ?>
            <div class="message-container">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

    <?php elseif ($role === 'admin'): ?>
        <!-- Liste des rendez-vous pour les administrateurs -->
        <div class="title1">
            <h1 class="title2">Rendez-vous du jour</h1>
        </div>
        <?php if (!empty($rendezvous_today)): ?>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Service</th>
                        <th>Horaire</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rendezvous_today as $rdv): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rdv['utilisateur_nom']); ?></td>
                            <td><?php echo htmlspecialchars($rdv['utilisateur_prenom']); ?></td>
                            <td><?php echo htmlspecialchars($rdv['service_nom']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($rdv['date_rdv'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="message-container">
                <p>Pas de rendez-vous pour aujourd'hui.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    const dateInput = document.getElementById('date');
    const timeSelect = document.getElementById('time');

    // Limite la date à 2 semaines à partir d'aujourd'hui
    const today = new Date();
    const maxDate = new Date(today);
    maxDate.setDate(today.getDate() + 14);

    dateInput.min = today.toISOString().split('T')[0];
    dateInput.max = maxDate.toISOString().split('T')[0];

    // Charger les horaires disponibles lorsque la date change
    dateInput.addEventListener('change', async () => {
        const selectedDate = dateInput.value;
        timeSelect.innerHTML = ""; // Réinitialise les options horaires

        if (!selectedDate) {
            timeSelect.innerHTML = "<option value=''>Sélectionnez une date d'abord</option>";
            return;
        }

        try {
            const response = await fetch('get_reserved_slots.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ date: selectedDate }),
            });
            const reservedSlots = await response.json();

            for (let hour = 9; hour <= 20; hour++) {
                if (!reservedSlots.includes(hour)) {
                    const option = document.createElement('option');
                    option.value = `${hour.toString().padStart(2, '0')}:00 - ${(hour + 1).toString().padStart(2, '0')}:00`;
                    option.textContent = `${hour.toString().padStart(2, '0')}:00 - ${(hour + 1).toString().padStart(2, '0')}:00`;
                    timeSelect.appendChild(option);
                }
            }

            if (timeSelect.options.length === 0) {
                timeSelect.innerHTML = "<option value=''>Aucun créneau disponible</option>";
            }
        } catch (error) {
            console.error('Erreur lors de la récupération des créneaux réservés:', error);
            timeSelect.innerHTML = "<option value=''>Erreur, veuillez réessayer</option>";
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const burgerMenu = document.getElementById('burger-menu');
        const navMenu = document.querySelector('.navigation-menu');

        burgerMenu.addEventListener('click', () => {
            navMenu.classList.toggle('active'); // Affiche/Cache le menu
        });
    });
</script>