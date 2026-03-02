<?php
$pageTitle = "Formations — Form'Campus";
include 'header.php';
include 'connexion.php';

$stmt = $bdd->query("SELECT f.*, c.nom AS categorie FROM formations f LEFT JOIN categories c ON f.id_categorie = c.id ORDER BY f.created_at DESC");
$formations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Images par catégorie
$catImages = [
    'Développement Web' => 'https://images.unsplash.com/photo-1593720213428-28a5b9e94613?w=500&q=80',
    'Data Science'      => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=500&q=80',
    'Design'            => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=500&q=80',
    'Marketing Digital' => 'https://images.unsplash.com/photo-1432888622747-4eb9a8efeb07?w=500&q=80',
    'default'           => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=500&q=80',
];
?>

<main>

<section class="page-hero">
    <div class="section-inner">
        <span class="eyebrow">Catalogue 2024</span>
        <h1>Nos <span class="highlight">Formations</span></h1>
        <p>Développez vos compétences avec nos programmes certifiants conçus par des experts.</p>
    </div>
    <div class="page-hero-bg">
        <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=1400&q=60" alt="Formation">
    </div>
</section>

<section class="formations-section">
    <div class="section-inner">
        <div class="formations-grid">
            <?php if ($formations): ?>
                <?php foreach ($formations as $f):
                    $img = $catImages[$f['categorie']] ?? $catImages['default'];
                ?>
                <div class="card-formation">
                    <div class="card-img-wrap">
                        <img src="<?= $img ?>" alt="<?= htmlspecialchars($f['titre']) ?>">
                        <div class="card-badge"><?= htmlspecialchars($f['categorie'] ?? 'Général') ?></div>
                    </div>
                    <div class="card-body">
                        <div class="card-level level-<?= mb_strtolower(str_replace(['é','É'], 'e', $f['niveau'])) ?>">
                            <?= htmlspecialchars($f['niveau']) ?>
                        </div>
                        <h3 class="card-title"><?= htmlspecialchars($f['titre']) ?></h3>
                        <p class="card-desc"><?= htmlspecialchars($f['description']) ?></p>
                        <div class="card-meta">
                            <span class="meta-item">⏱ <?= htmlspecialchars($f['duree']) ?></span>
                            <span class="meta-item">👥 <?= (int)$f['places_disponibles'] ?> places</span>
                            <span class="meta-item">📅 <?= date('d/m/Y', strtotime($f['date_debut'])) ?></span>
                        </div>
                        <div class="card-footer-row">
                            <span class="card-price"><?= number_format($f['prix'], 2) ?> DH</span>
                            <a href="inscription.php?formation=<?= (int)$f['id'] ?>" class="btn-card">S'inscrire →</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=300&q=70" alt="Aucune formation">
                    <p>Aucune formation disponible pour le moment.</p>
                    <a href="index.php" class="btn-primary">Retour à l'accueil</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

</main>

<?php require 'footer.php'; ?>