<?php
require_once 'elements/header1.php';
?>
    <form action="reinitialise.php" method="post" class="co">
        <fieldset id="connexion">
            <div style="padding-bottom: 20px; font-size: 36px; text-align:center;"><h3>Réinitialiser le mot de passe</h3></div>
            <div id="div_email">
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <button type="submit">Réinitialiser le mot de passe</button><br>
        </fieldset>
    </form>
