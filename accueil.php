<?php
require_once "elements/header.php";
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