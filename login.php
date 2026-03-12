<?php
$pageTitle = "Connexion — Form'Campus";
include 'header.php';
include 'connexion.php';
session_start();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['mot_de_passe'] ?? '';

    if (!$email || !$password) {
        $message = "Veuillez remplir tous les champs.";
        $messageType = 'error';
    } else {
        $stmt = $bdd->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_nom']  = $user['prenom'] . ' ' . $user['nom'];
            $_SESSION['user_role'] = $user['role'];

            // ✅ Redirection selon le rôle
            if ($user['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $message = "Email ou mot de passe incorrect.";
            $messageType = 'error';
        }
    }
}
?>

<main>
<section class="auth-page">
    <div class="auth-visual">
        <img src="https://images.unsplash.com/photo-1516321497487-e288fb19713f?w=800&q=80" alt="Connexion">
        <div class="auth-visual-overlay">
            <div class="auth-visual-text">
                <h2>Content de vous revoir !</h2>
                <p>Accédez à votre espace personnel et continuez votre apprentissage.</p>
                <ul class="auth-perks">
                    <li>📚 Vos cours en un clic</li>
                    <li>📈 Suivez votre progression</li>
                    <li>🎓 Téléchargez vos certificats</li>
                    <li>🔐 Espace admin sécurisé</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="auth-form-wrap auth-form-wrap--center">
        <div class="auth-box">
            <div class="auth-header">
                <div class="auth-icon">🔐</div>
                <h2>Bon retour !</h2>
                <p>Connectez-vous à votre espace</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label>Adresse email</label>
                    <input type="email" name="email" placeholder="exemple@email.com" required
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="mot_de_passe" placeholder="Votre mot de passe" required>
                </div>
                <button type="submit" class="btn-submit">Se connecter →</button>
            </form>

            <p class="auth-switch">Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
        </div>
    </div>
</section>
</main>

<?php require 'footer.php'; ?>