<?php
$pageTitle = "Inscription — Form'Campus";
include 'header.php';
include 'connexion.php';

$message = '';
$messageType = '';

$stmt = $bdd->query("SELECT id, titre FROM formations ORDER BY titre");
$formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
$formation_id = isset($_GET['formation']) ? (int)$_GET['formation'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom       = trim($_POST['nom'] ?? '');
    $prenom    = trim($_POST['prenom'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password  = $_POST['mot_de_passe'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';
    $id_form   = (int)($_POST['id_formation'] ?? 0);

    if (!$nom || !$prenom || !$email || !$password || !$id_form) {
        $message = "Veuillez remplir tous les champs obligatoires.";
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "L'adresse email n'est pas valide.";
        $messageType = 'error';
    } elseif ($password !== $confirm) {
        $message = "Les mots de passe ne correspondent pas.";
        $messageType = 'error';
    } elseif (strlen($password) < 6) {
        $message = "Le mot de passe doit contenir au moins 6 caractères.";
        $messageType = 'error';
    } else {
        $check = $bdd->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $message = "Cette adresse email est déjà utilisée.";
            $messageType = 'error';
        } else {
            $hash   = password_hash($password, PASSWORD_DEFAULT);
            $insert = $bdd->prepare("INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe) VALUES (?,?,?,?,?)");
            $insert->execute([$nom, $prenom, $email, $telephone, $hash]);
            $user_id = $bdd->lastInsertId();
            $inscrire = $bdd->prepare("INSERT INTO inscriptions (id_utilisateur, id_formation) VALUES (?,?)");
            $inscrire->execute([$user_id, $id_form]);
            $message = '🎉 Inscription réussie ! <a href="login.php">Se connecter maintenant</a>';
            $messageType = 'success';
        }
    }
}
?>

<main>
<section class="auth-page">
    <div class="auth-visual">
        <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=800&q=80" alt="Inscription">
        <div class="auth-visual-overlay">
            <div class="auth-visual-text">
                <h2>Commencez dès aujourd'hui</h2>
                <p>Rejoignez notre communauté et transformez votre carrière.</p>
                <ul class="auth-perks">
                    <li>✅ Accès immédiat aux cours</li>
                    <li>✅ Certificat à la fin</li>
                    <li>✅ Support 7j/7</li>
                    <li>✅ Communauté d'apprenants</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="auth-form-wrap">
        <div class="auth-box">
            <div class="auth-header">
                <div class="auth-icon">🎓</div>
                <h2>Créer un compte</h2>
                <p>Remplissez le formulaire pour commencer</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nom <span class="req">*</span></label>
                        <input type="text" name="nom" placeholder="Votre nom" required
                               value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Prénom <span class="req">*</span></label>
                        <input type="text" name="prenom" placeholder="Votre prénom" required
                               value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Email <span class="req">*</span></label>
                    <input type="email" name="email" placeholder="exemple@email.com" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="telephone" placeholder="06 XX XX XX XX"
                           value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Formation choisie <span class="req">*</span></label>
                    <select name="id_formation" required>
                        <option value="">-- Sélectionnez une formation --</option>
                        <?php foreach ($formations as $f): ?>
                            <option value="<?= $f['id'] ?>"
                                <?= ($formation_id == $f['id'] || (isset($_POST['id_formation']) && $_POST['id_formation'] == $f['id'])) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($f['titre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Mot de passe <span class="req">*</span></label>
                        <input type="password" name="mot_de_passe" placeholder="Min. 6 caractères" required>
                    </div>
                    <div class="form-group">
                        <label>Confirmer <span class="req">*</span></label>
                        <input type="password" name="confirm_password" placeholder="Répétez" required>
                    </div>
                </div>
                <button type="submit" class="btn-submit">Créer mon compte →</button>
            </form>
            <p class="auth-switch">Déjà un compte ? <a href="login.php">Se connecter</a></p>
        </div>
    </div>
</section>
</main>

<?php require 'footer.php'; ?>