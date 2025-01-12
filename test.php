<?php

$motDePasseClair = "user123";

$monVraiHash = '$2y$10$y73psMA0Hi6rfAXolLUZwOjnhBJHQFkB9SG/vPf7rCJtSOE/L591e'; // <= Copie EXACTEMENT le hash depuis la BDD

if (password_verify($motDePasseClair, $monVraiHash)) {
    echo "Le password_verify() fonctionne !";
} else {
    echo "password_verify() échoue…";
}