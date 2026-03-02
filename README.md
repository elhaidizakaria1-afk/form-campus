# 🎓 Form'Campus

Plateforme de formations en ligne au Maroc, développée en PHP/MySQL.

## Technologies
- PHP 8+
- MySQL
- CSS (custom, sans framework)

## Installation

1. Clonez le repo :
   git clone https://github.com/votre-nom/formcampus.git

2. Importez la base de données :
   mysql -u root -p < formcampus.sql

3. Configurez la connexion :
   cp connexion.example.php connexion.php
   # Éditez connexion.php avec vos identifiants

4. Lancez avec XAMPP ou WAMP et accédez à localhost/formcampus

## Structure
- index.php — Page d'accueil
- formations.php — Catalogue des formations
- inscription.php — Inscription utilisateur
- login.php / logout.php — Authentification
- admin.php — Tableau de bord admin
- header.php / footer.php — Composants partagés
- style.css — Styles globaux