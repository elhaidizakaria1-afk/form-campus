<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title><?= $pageTitle ?? "Form'Campus" ?></title>
</head>
<body>
<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<header>
    <div class="container">
        <a href="index.php" class="logo">🎓 <span class="span1">Form</span>'Campus</a>
        <nav>
            <ul>
                <li><a href="index.php"       class="<?= $currentPage==='index.php'       ?'active':'' ?>">Accueil</a></li>
                <li><a href="formations.php"  class="<?= $currentPage==='formations.php'  ?'active':'' ?>">Formations</a></li>
                <li><a href="inscription.php" class="<?= $currentPage==='inscription.php' ?'active':'' ?>">S'inscrire</a></li>
                <li><a href="login.php"       class="btn-nav <?= $currentPage==='login.php' ?'active':'' ?>">Se connecter</a></li>
            </ul>
        </nav>
    </div>
</header>