<?php
require_once "elements/header1.php";
?>
    <div class="title1">
        <h1 class="title2">La prise de Rendez-vous facilitée</h1>
    </div>
    <form action="login.php" method="post" class="co">
        <fieldset id="connexion">
            <div style="padding-bottom: 20px; font-size: 36px; text-align:center;"><h3>Connexion</h3></div>
            <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                <p style="color: red; text-align: center;">Email ou mot de passe invalide</p>
            <?php endif; ?>

            <div id="div_email">
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div id="div_password">
                <input type="password" id="password" name="password" placeholder="Mot de passe" required>
            </div>
            <div class="Fpassword">
                <a href="oublie.php" class="mdp">Mot de passe oublié</a>
            </div>
            <button type="submit">Connexion</button><br>
            <button type="button"><a href="creation.php" style="text-decoration: none; color: black;">Créer un compte</a></button>
        </fieldset>
    </form>
    <script>
        // Supprime le paramètre "error" de l'URL après affichage
        if (window.location.search.includes("error=1")) {
            const url = new URL(window.location);
            url.searchParams.delete("error");
            window.history.replaceState({}, document.title, url.toString());
        }
    </script>
</body>
</html>