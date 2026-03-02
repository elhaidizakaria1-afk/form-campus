<?php
$pageTitle = "Accueil — Form'Campus";
include 'header.php';
?>

<main>

<!-- ═══ HERO ═══ -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-text">
            <span class="eyebrow">✦ Plateforme N°1 au Maroc</span>
            <h1 class="hero-title">
                <span class="highlight">Développez</span><br>
                vos compétences,<br>
                changez votre avenir
            </h1>
            <p class="hero-para">
                Accédez à des formations certifiantes conçues par des experts. Apprenez à votre rythme, obtenez votre certification et boostez votre carrière.
            </p>
            <div class="hero-actions">
                <a href="formations.php" class="btn-primary">Découvrir les formations →</a>
                <a href="inscription.php" class="btn-ghost">Créer un compte</a>
            </div>
            <div class="hero-stats">
                <div class="stat"><span>1 200+</span><p>Étudiants</p></div>
                <div class="stat-sep"></div>
                <div class="stat"><span>40+</span><p>Formations</p></div>
                <div class="stat-sep"></div>
                <div class="stat"><span>95%</span><p>Satisfaction</p></div>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-glow"></div>
            <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=700&q=80"
                    alt="Étudiants en formation" class="hero-img">
            <div class="hero-card hc1">
                <span class="hc-icon">🏆</span>
                <div><strong>Certifié</strong><p>Diplôme reconnu</p></div>
            </div>
            <div class="hero-card hc2">
                <span class="hc-icon">⚡</span>
                <div><strong>100% en ligne</strong><p>Apprenez partout</p></div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ CATÉGORIES ═══ -->
<section class="categories">
    <div class="section-inner">
        <div class="section-header">
            <span class="eyebrow">Nos domaines</span>
            <h2>Explorez par <span class="highlight">catégorie</span></h2>
        </div>
        <div class="categories-grid">
            <a href="formations.php" class="cat-card">
                <img src="https://images.unsplash.com/photo-1593720213428-28a5b9e94613?w=500&q=80" alt="Développement Web">
                <div class="cat-overlay">
                    <span class="cat-icon">💻</span>
                    <h3>Développement Web</h3>
                    <p>HTML, CSS, PHP, JavaScript</p>
                </div>
            </a>
            <a href="formations.php" class="cat-card">
                <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=500&q=80" alt="Data Science">
                <div class="cat-overlay">
                    <span class="cat-icon">📊</span>
                    <h3>Data Science</h3>
                    <p>Python, ML, BI, Analytics</p>
                </div>
            </a>
            <a href="formations.php" class="cat-card">
                <img src="https://images.unsplash.com/photo-1561070791-2526d30994b5?w=500&q=80" alt="Design UI/UX">
                <div class="cat-overlay">
                    <span class="cat-icon">🎨</span>
                    <h3>Design UI/UX</h3>
                    <p>Figma, Prototypage, UX</p>
                </div>
            </a>
            <a href="formations.php" class="cat-card">
                <img src="https://images.unsplash.com/photo-1432888622747-4eb9a8efeb07?w=500&q=80" alt="Marketing Digital">
                <div class="cat-overlay">
                    <span class="cat-icon">📱</span>
                    <h3>Marketing Digital</h3>
                    <p>SEO, Social Media, Ads</p>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- ═══ POURQUOI NOUS ═══ -->
<section class="why">
    <div class="section-inner why-inner">
        <div class="why-visual">
            <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?w=600&q=80" alt="Apprentissage en ligne">
            <div class="why-badge"><strong>+3 ans</strong><p>d'expérience</p></div>
        </div>
        <div class="why-text">
            <span class="eyebrow">Pourquoi nous choisir</span>
            <h2>Une formation pensée pour <span class="highlight">votre réussite</span></h2>
            <div class="why-features">
                <div class="feature">
                    <span class="feat-icon">🎯</span>
                    <div><h4>Programmes certifiants</h4><p>Nos diplômes sont reconnus par les entreprises partenaires.</p></div>
                </div>
                <div class="feature">
                    <span class="feat-icon">👨‍🏫</span>
                    <div><h4>Formateurs experts</h4><p>Des professionnels actifs qui transmettent leur expérience réelle.</p></div>
                </div>
                <div class="feature">
                    <span class="feat-icon">⏰</span>
                    <div><h4>Flexibilité totale</h4><p>Apprenez à votre rythme, depuis n'importe où, sur tous vos appareils.</p></div>
                </div>
                <div class="feature">
                    <span class="feat-icon">🤝</span>
                    <div><h4>Accompagnement personnalisé</h4><p>Un suivi individuel pour vous aider à atteindre vos objectifs.</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ TÉMOIGNAGES ═══ -->
<section class="testimonials">
    <div class="section-inner">
        <div class="section-header">
            <span class="eyebrow">Ils nous font confiance</span>
            <h2>Ce que disent nos <span class="highlight">étudiants</span></h2>
        </div>
        <div class="testi-grid">
            <div class="testi-card">
                <div class="testi-stars">★★★★★</div>
                <p>"La formation Développement Web m'a permis de trouver un emploi en 2 mois. Le contenu est vraiment de qualité et les formateurs sont très disponibles."</p>
                <div class="testi-author">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&h=60&fit=crop&q=80" alt="Youssef">
                    <div><strong>Youssef B.</strong><p>Développeur Web Junior</p></div>
                </div>
            </div>
            <div class="testi-card testi-featured">
                <div class="testi-stars">★★★★★</div>
                <p>"J'ai appris la Data Science depuis zéro. Aujourd'hui je travaille comme analyste dans une entreprise internationale. Merci Form'Campus !"</p>
                <div class="testi-author">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=60&h=60&fit=crop&q=80" alt="Sarah">
                    <div><strong>Sarah M.</strong><p>Data Analyst</p></div>
                </div>
            </div>
            <div class="testi-card">
                <div class="testi-stars">★★★★★</div>
                <p>"Le cours UI/UX Design est excellent. La plateforme est intuitive et le suivi est remarquable. Je recommande vivement !"</p>
                <div class="testi-author">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=60&h=60&fit=crop&q=80" alt="Karim">
                    <div><strong>Karim A.</strong><p>Designer UI/UX</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ CTA ═══ -->
<section class="cta-section">
    <div class="section-inner cta-inner">
        <div class="cta-text">
            <h2>Prêt à commencer votre <span class="highlight">aventure</span> ?</h2>
            <p>Rejoignez plus de 1 200 étudiants qui ont transformé leur carrière.</p>
        </div>
        <div class="cta-actions">
            <a href="inscription.php" class="btn-primary btn-lg">S'inscrire gratuitement →</a>
            <a href="formations.php" class="btn-ghost">Voir les formations</a>
        </div>
    </div>
</section>

</main>

<?php require 'footer.php'; ?>