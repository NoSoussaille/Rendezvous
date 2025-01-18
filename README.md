# Projet : Application de gestion des rendez-vous

## Description
Ce projet est une application web de gestion des rendez-vous. Elle permet aux utilisateurs de prendre des rendez-vous et fournit un tableau de bord pour les administrateurs, incluant des statistiques sur les rendez-vous.

L'application utilise à la fois une base de données relationnelle (MySQL) et une base de données NoSQL (MongoDB), offrant une démonstration pratique de la gestion des données dans un environnement mixte.

---

## Fonctionnalités principales

### Côté utilisateur
- Prise de rendez-vous en ligne.
- Interface intuitive et responsive.

### Côté administrateur
- Tableau de bord sécurisé.
- Statistiques dynamiques sur les rendez-vous :
  - Nombre total de rendez-vous.
  - Répartition par date.
  - Répartition par service.
- Synchronisation des données entre MySQL et MongoDB.

---

## Technologies utilisées

### Front-end
- **HTML5**, **CSS3**, **JavaScript** : pour l'interface utilisateur.
- Responsiveness : Gestion de l'affichage adaptatif sur différents appareils.

### Back-end
- **PHP** : pour la logique serveur et la gestion des sessions.

### Bases de données
- **MySQL** : pour la gestion relationnelle des données.
- **MongoDB** : pour les statistiques et la gestion NoSQL.

### Outils
- **Composer** : Gestion des dépendances PHP.
- **Git** : Gestion du versionnement.
- **MAMP** : Environnement de développement local.

---

## Installation et configuration

### Prérequis
- PHP 8 ou supérieur.
- MySQL 5.x ou supérieur.
- MongoDB 5.x ou supérieur.
- Composer.
- Serveur local (ex. : MAMP, XAMPP).

### Étapes

1. **Cloner le dépôt :**
   ```bash
   git clone <URL_DU_DÉPÔT>
   cd <NOM_DU_PROJET>
   ```

2. **Installer les dépendances :**
   ```bash
   composer install
   ```

3. **Configurer MySQL :**
   - Importez le fichier `database.sql` dans votre instance MySQL.
   - Configurez le fichier `config.php` avec vos informations de connexion MySQL :
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'sql_database');
     define('DB_USER', 'root');
     define('DB_PASS', 'root');
     ```

4. **Configurer MongoDB :**
   Assurez-vous que MongoDB est en cours d'exécution sur `127.0.0.1:27017`.

5. **Démarrer l'application :**
   Placez le projet dans le répertoire racine de votre serveur local (ex. : `htdocs` pour MAMP).
   Accédez à l'application via `http://localhost/<NOM_DU_PROJET>`.

---

## Fonctionnalités techniques

### Synchronisation des données
Un bouton dans le tableau de bord permet de synchroniser les données entre MySQL et MongoDB. Les rendez-vous de MySQL sont insérés ou mis à jour dans MongoDB via la méthode `upsert`.

### Statistiques
Les statistiques dynamiques sont affichées en utilisant les données de MongoDB. Un système d'onglets permet de filtrer les données par année, et les dates sont formatées au format `JJ/MM/AAAA`.

---

## Sécurité
- Gestion des sessions pour limiter l'accès aux administrateurs.
- Requêtes préparées pour prévenir les injections SQL.
- Utilisation de MongoDB pour les opérations non critiques afin de minimiser la charge sur MySQL.

---

## Déploiement

1. **Configurer un serveur de production :**
   - PHP, MySQL et MongoDB doivent être installés.
   - Configurez les permissions pour assurer l'accès aux fichiers nécessaires.

2. **Déploiement des fichiers :**
   - Téléversez les fichiers via FTP ou Git.
   - Mettez à jour les configurations dans `config.php` pour correspondre à l'environnement de production.

3. **Tester :**
   - Vérifiez que les données sont bien synchronisées entre MySQL et MongoDB.
   - Assurez-vous que toutes les fonctionnalités fonctionnent correctement.

---

## Auteur
Marwane

---
