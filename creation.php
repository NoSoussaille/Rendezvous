<?php
require_once 'elements/header1.php';
?>
<form action="#" method="post" class="co" style="margin-top: 50px;" onsubmit="return validatePasswords()">
    <fieldset id="connexion">
        <div style="padding-bottom: 20px; font-size: 36px; text-align:center;">
            <h3>Création de compte</h3>
        </div>
        <div id="div_nom">
            <label for="name">Nom</label><br>
            <input type="text" id="name" name="name" placeholder="Nom" required>
        </div>
        <div id="div_prenom">
            <label for="firstname">Prénom</label><br>
            <input type="text" id="firstname" name="firstname" placeholder="Prénom" required>
        </div>
        <div id="div_email">
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" placeholder="Email" required>
        </div>
        <div id="div_password">
            <label for="password">Mot de passe</label><br>
            <input type="password" id="password" name="password" placeholder="Mot de passe" required>
        </div>
        <div id="div_password2">
            <label for="password2">Confirmer le mot de passe</label><br>
            <input type="password" id="password2" name="password2" placeholder="Confirmer le mot de passe" required>
            <p id="password-error" style="color: red; font-size: 16px; margin: 5px; text-align: center;"></p>
        </div>
        <button type="submit" style="margin-top: 20px;">Créer un compte</button><br>
    </fieldset>
</form>
    <script>
        // Supprime le paramètre "error" de l'URL après affichage
        if (window.location.search.includes("error=1")) {
            const url = new URL(window.location);
            url.searchParams.delete("error");
            window.history.replaceState({}, document.title, url.toString());
        }

        function validatePasswords() {
            // Récupère les champs de mot de passe et le conteneur pour l'erreur
            const password = document.getElementById("password").value;
            const password2 = document.getElementById("password2").value;
            const errorContainer = document.getElementById("password-error");

            // Réinitialise le message d'erreur
            errorContainer.textContent = "";

            // Vérifie si les mots de passe correspondent
            if (password !== password2) {
                errorContainer.textContent = "Les mots de passe ne correspondent pas.";
                return false; // Empêche la soumission du formulaire
            }

            return true; // Permet la soumission si tout est correct
        }
    </script>
</body>
</html>