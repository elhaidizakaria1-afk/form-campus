<?php
session_start();
include 'connexion.php';

// ── Sécurité : seuls les admins peuvent accéder ──
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$msg = '';
$msgType = '';
$tab = $_GET['tab'] ?? 'dashboard';

// ════════════════════════════════════════════
//   ACTIONS POST
// ════════════════════════════════════════════

// --- FORMATIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ajouter formation
    if (isset($_POST['add_formation'])) {
        $stmt = $bdd->prepare("INSERT INTO formations (titre, description, duree, prix, places_disponibles, date_debut, date_fin, niveau, id_categorie) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $_POST['titre'], $_POST['description'], $_POST['duree'],
            $_POST['prix'],  $_POST['places'],      $_POST['date_debut'],
            $_POST['date_fin'], $_POST['niveau'],   $_POST['id_categorie']
        ]);
        $msg = "Formation ajoutée avec succès !"; $msgType = 'success';
        $tab = 'formations';
    }

    // Modifier formation
    if (isset($_POST['edit_formation'])) {
        $stmt = $bdd->prepare("UPDATE formations SET titre=?, description=?, duree=?, prix=?, places_disponibles=?, date_debut=?, date_fin=?, niveau=?, id_categorie=? WHERE id=?");
        $stmt->execute([
            $_POST['titre'], $_POST['description'], $_POST['duree'],
            $_POST['prix'],  $_POST['places'],      $_POST['date_debut'],
            $_POST['date_fin'], $_POST['niveau'],   $_POST['id_categorie'], $_POST['id']
        ]);
        $msg = "Formation modifiée avec succès !"; $msgType = 'success';
        $tab = 'formations';
    }

    // Supprimer formation
    if (isset($_POST['delete_formation'])) {
        $bdd->prepare("DELETE FROM formations WHERE id=?")->execute([$_POST['id']]);
        $msg = "Formation supprimée."; $msgType = 'error';
        $tab = 'formations';
    }

    // Modifier statut inscription
    if (isset($_POST['update_statut'])) {
        $bdd->prepare("UPDATE inscriptions SET statut=? WHERE id=?")->execute([$_POST['statut'], $_POST['id']]);
        $msg = "Statut mis à jour !"; $msgType = 'success';
        $tab = 'inscriptions';
    }

    // Supprimer utilisateur
    if (isset($_POST['delete_user'])) {
        $bdd->prepare("DELETE FROM utilisateurs WHERE id=? AND role='etudiant'")->execute([$_POST['id']]);
        $msg = "Étudiant supprimé."; $msgType = 'error';
        $tab = 'etudiants';
    }

    // Ajouter catégorie
    if (isset($_POST['add_categorie'])) {
        $bdd->prepare("INSERT INTO categories (nom, description) VALUES (?,?)")->execute([$_POST['nom'], $_POST['description']]);
        $msg = "Catégorie ajoutée !"; $msgType = 'success';
        $tab = 'categories';
    }

    // Supprimer catégorie
    if (isset($_POST['delete_categorie'])) {
        $bdd->prepare("DELETE FROM categories WHERE id=?")->execute([$_POST['id']]);
        $msg = "Catégorie supprimée."; $msgType = 'error';
        $tab = 'categories';
    }
}

// ════════════════════════════════════════════
//   DONNÉES
// ════════════════════════════════════════════
$formations   = $bdd->query("SELECT f.*, c.nom AS cat FROM formations f LEFT JOIN categories c ON f.id_categorie=c.id ORDER BY f.id DESC")->fetchAll(PDO::FETCH_ASSOC);
$categories   = $bdd->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$etudiants    = $bdd->query("SELECT * FROM utilisateurs WHERE role='etudiant' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$inscriptions = $bdd->query("SELECT i.*, CONCAT(u.prenom,' ',u.nom) AS etudiant, f.titre AS formation FROM inscriptions i JOIN utilisateurs u ON i.id_utilisateur=u.id JOIN formations f ON i.id_formation=f.id ORDER BY i.date_inscription DESC")->fetchAll(PDO::FETCH_ASSOC);

// Stats
$nbFormations   = count($formations);
$nbEtudiants    = count($etudiants);
$nbInscriptions = count($inscriptions);
$nbConfirmees   = count(array_filter($inscriptions, fn($i) => $i['statut'] === 'confirmée'));

// Edition formation
$editFormation = null;
if (isset($_GET['edit']) && $_GET['tab'] === 'formations') {
    $s = $bdd->prepare("SELECT * FROM formations WHERE id=?");
    $s->execute([$_GET['edit']]);
    $editFormation = $s->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
    <title>Dashboard Admin — Form'Campus</title>
    <style>
        /* ── Variables ── */
        :root {
            --teal:#08CCA8; --teal-d:#06a888; --teal-g:rgba(8,204,168,0.18);
            --black:#07100e; --dark:#0d1714; --dark2:#142320;
            --surface:#192b27; --surface2:#1f3430;
            --border:rgba(8,204,168,0.14); --border2:rgba(255,255,255,0.07);
            --text:#ddf0ec; --muted:#6fa89d; --white:#fff;
            --red:#ef4444; --green:#22c55e; --yellow:#f59e0b;
            --radius:14px; --trans:all .3s cubic-bezier(.4,0,.2,1);
        }
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'DM Sans',sans-serif;background:var(--black);color:var(--text);min-height:100vh;display:flex}
        a{text-decoration:none;color:inherit}
        ::selection{background:var(--teal);color:var(--black)}
        ::-webkit-scrollbar{width:5px}
        ::-webkit-scrollbar-thumb{background:var(--teal);border-radius:3px}

        /* ── Sidebar ── */
        .sidebar {
            width: 260px; min-height: 100vh;
            background: var(--dark);
            border-right: 1px solid var(--border);
            display: flex; flex-direction: column;
            position: sticky; top: 0; height: 100vh;
            overflow-y: auto; flex-shrink: 0;
        }
        .sb-brand {
            padding: 1.8rem 1.5rem 1.2rem;
            font-family: 'Syne', sans-serif;
            font-weight: 800; font-size: 1.2rem;
            color: var(--white);
            border-bottom: 1px solid var(--border);
        }
        .sb-brand span { color: var(--teal); }
        .sb-role {
            font-family: 'DM Sans', sans-serif;
            font-weight: 400; font-size: 0.75rem;
            color: var(--teal);
            background: rgba(8,204,168,0.1);
            border: 1px solid var(--border);
            padding: 3px 10px; border-radius: 50px;
            display: inline-block; margin-top: 6px;
        }
        .sb-nav { padding: 1rem 0.8rem; flex: 1; }
        .sb-label {
            font-size: 0.65rem; letter-spacing: 2px;
            text-transform: uppercase; color: var(--muted);
            padding: 0.5rem 0.7rem; margin-top: 0.8rem;
        }
        .sb-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; border-radius: var(--radius);
            color: var(--muted); font-size: 0.88rem;
            font-weight: 500; transition: var(--trans);
            margin-bottom: 2px;
        }
        .sb-link:hover, .sb-link.active {
            background: var(--teal-g);
            color: var(--teal);
        }
        .sb-link.active { border-left: 2px solid var(--teal); padding-left: 12px; }
        .sb-icon { font-size: 1.1rem; }
        .sb-bottom { padding: 1rem; border-top: 1px solid var(--border); }
        .sb-user {
            display: flex; align-items: center; gap: 10px;
            padding: 10px; border-radius: var(--radius);
            background: var(--surface);
            border: 1px solid var(--border);
        }
        .sb-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--teal); color: var(--black);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif; font-weight: 800;
            font-size: 0.9rem; flex-shrink: 0;
        }
        .sb-user-name { font-size: 0.82rem; font-weight: 500; color: var(--white); }
        .sb-user-role { font-size: 0.72rem; color: var(--muted); }
        .logout-btn {
            display: flex; align-items: center; gap: 8px;
            margin-top: 0.6rem; padding: 10px 14px;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.18);
            border-radius: var(--radius); color: #f87171;
            font-size: 0.85rem; font-weight: 500;
            cursor: pointer; transition: var(--trans); width: 100%;
        }
        .logout-btn:hover { background: rgba(239,68,68,0.15); }

        /* ── Main ── */
        .main { flex: 1; overflow-y: auto; }

        .topbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.2rem 2rem;
            background: rgba(13,23,20,0.8);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(16px);
            position: sticky; top: 0; z-index: 10;
        }
        .topbar h1 {
            font-family: 'Syne', sans-serif;
            font-weight: 800; font-size: 1.4rem;
            color: var(--white);
        }
        .topbar-date { font-size: 0.82rem; color: var(--muted); }

        .content { padding: 2rem; }

        /* Alert */
        .alert {
            padding: 13px 18px; border-radius: var(--radius);
            font-size: 0.87rem; margin-bottom: 1.5rem;
            border: 1px solid transparent;
        }
        .alert-success { background:rgba(34,197,94,.1); border-color:rgba(34,197,94,.25); color:#4ade80; }
        .alert-error   { background:rgba(239,68,68,.1);  border-color:rgba(239,68,68,.25);  color:#f87171; }

        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.2rem; margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            transition: var(--trans);
        }
        .stat-card:hover { border-color: rgba(8,204,168,.3); transform: translateY(-3px); }
        .stat-card-icon { font-size: 1.8rem; margin-bottom: 0.8rem; }
        .stat-card-val {
            font-family: 'Syne', sans-serif;
            font-weight: 800; font-size: 2.2rem;
            color: var(--teal); letter-spacing: -1px;
        }
        .stat-card-lbl { font-size: 0.82rem; color: var(--muted); margin-top: 4px; }

        /* Section */
        .section-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden; margin-bottom: 2rem;
        }
        .section-box-head {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.3rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        .section-box-head h2 {
            font-family: 'Syne', sans-serif;
            font-weight: 700; font-size: 1rem;
            color: var(--white);
        }

        /* Table */
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th {
            text-align: left; padding: 12px 16px;
            font-size: 0.72rem; letter-spacing: 1.5px;
            text-transform: uppercase; color: var(--muted);
            background: rgba(0,0,0,.2);
            border-bottom: 1px solid var(--border);
        }
        .admin-table td {
            padding: 13px 16px;
            font-size: 0.87rem;
            border-bottom: 1px solid var(--border2);
            vertical-align: middle;
        }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover td { background: rgba(8,204,168,.03); }

        /* Badges */
        .badge {
            display: inline-block;
            font-size: 0.7rem; font-weight: 500;
            padding: 3px 10px; border-radius: 50px;
        }
        .badge-green  { background:rgba(34,197,94,.12);  color:#4ade80; border:1px solid rgba(34,197,94,.2); }
        .badge-yellow { background:rgba(245,158,11,.12); color:#fbbf24; border:1px solid rgba(245,158,11,.2); }
        .badge-red    { background:rgba(239,68,68,.12);  color:#f87171; border:1px solid rgba(239,68,68,.2); }
        .badge-teal   { background:rgba(8,204,168,.12);  color:var(--teal); border:1px solid var(--border); }

        /* Buttons */
        .btn { display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:0.8rem;font-weight:500;cursor:pointer;border:none;transition:var(--trans); }
        .btn-teal  { background:var(--teal);color:var(--black);font-family:'Syne',sans-serif;font-weight:700; }
        .btn-teal:hover { background:var(--teal-d); }
        .btn-ghost { background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--border2); }
        .btn-ghost:hover { border-color:var(--teal);color:var(--teal); }
        .btn-red   { background:rgba(239,68,68,.12);color:#f87171;border:1px solid rgba(239,68,68,.2); }
        .btn-red:hover { background:rgba(239,68,68,.22); }
        .btn-sm    { padding:5px 10px; font-size:0.75rem; }

        /* Forms */
        .admin-form { padding: 1.5rem; }
        .form-grid  { display:grid;grid-template-columns:1fr 1fr;gap:1rem; }
        .form-grid-3{ display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem; }
        .fg { display:flex;flex-direction:column;gap:0.45rem; }
        .fg.full { grid-column: 1/-1; }
        .fg label { font-size:0.78rem;font-weight:500;color:var(--muted); }
        .fg input,.fg select,.fg textarea {
            background:rgba(255,255,255,.04);
            border:1px solid rgba(255,255,255,.09);
            color:var(--text); padding:11px 14px;
            border-radius:10px;
            font-family:'DM Sans',sans-serif;font-size:0.88rem;
            transition:var(--trans); outline:none; appearance:none;
        }
        .fg select { background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2308cca8' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E"); background-repeat:no-repeat;background-position:right 12px center;padding-right:36px;cursor:pointer; }
        .fg select option { background:var(--dark2); }
        .fg input:focus,.fg select:focus,.fg textarea:focus { border-color:var(--teal);background:rgba(8,204,168,.05);box-shadow:0 0 0 3px rgba(8,204,168,.1); }
        .fg input::placeholder,.fg textarea::placeholder { color:rgba(111,168,157,.35); }
        .fg textarea { resize:vertical; min-height:80px; }
        .form-actions { display:flex;gap:0.8rem;margin-top:1.2rem;padding-top:1.2rem;border-top:1px solid var(--border); }

        /* Tabs label */
        .page-tabs {
            display:flex;gap:0.4rem;margin-bottom:2rem;
            background:var(--surface);
            border:1px solid var(--border);
            border-radius:var(--radius);
            padding:6px; width:fit-content;
        }
        .page-tab {
            padding:8px 18px;border-radius:10px;
            font-size:0.85rem;font-weight:500;
            color:var(--muted);transition:var(--trans);
        }
        .page-tab.active,.page-tab:hover { background:var(--teal);color:var(--black); }

        /* Select statut inline */
        .sel-statut {
            background:transparent;border:1px solid var(--border2);
            color:var(--text);padding:5px 10px;
            border-radius:8px;font-size:0.8rem;cursor:pointer;
            appearance:none; outline:none;
        }
        .sel-statut:focus { border-color:var(--teal); }

        /* Responsive */
        @media(max-width:900px){
            .sidebar{display:none}
            .stats-grid{grid-template-columns:repeat(2,1fr)}
        }
        @media(max-width:600px){
            .stats-grid{grid-template-columns:1fr}
            .form-grid,.form-grid-3{grid-template-columns:1fr}
        }
    </style>
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar">
    <div class="sb-brand">
        🎓 <span>Form</span>'Campus
        <div class="sb-role">Panel Admin</div>
    </div>
    <nav class="sb-nav">
        <div class="sb-label">Navigation</div>
        <a href="?tab=dashboard"    class="sb-link <?= $tab==='dashboard'    ?'active':'' ?>"><span class="sb-icon">📊</span> Dashboard</a>
        <a href="?tab=formations"   class="sb-link <?= $tab==='formations'   ?'active':'' ?>"><span class="sb-icon">📚</span> Formations</a>
        <a href="?tab=categories"   class="sb-link <?= $tab==='categories'   ?'active':'' ?>"><span class="sb-icon">🗂️</span> Catégories</a>
        <a href="?tab=inscriptions" class="sb-link <?= $tab==='inscriptions' ?'active':'' ?>"><span class="sb-icon">📝</span> Inscriptions</a>
        <a href="?tab=etudiants"    class="sb-link <?= $tab==='etudiants'    ?'active':'' ?>"><span class="sb-icon">👥</span> Étudiants</a>

        <div class="sb-label">Site</div>
        <a href="index.php"       class="sb-link"><span class="sb-icon">🏠</span> Accueil</a>
        <a href="formations.php"  class="sb-link"><span class="sb-icon">👁️</span> Voir formations</a>
    </nav>
    <div class="sb-bottom">
        <div class="sb-user">
            <div class="sb-avatar"><?= strtoupper(substr($_SESSION['user_nom'],0,1)) ?></div>
            <div>
                <div class="sb-user-name"><?= htmlspecialchars($_SESSION['user_nom']) ?></div>
                <div class="sb-user-role">Administrateur</div>
            </div>
        </div>
        <form method="POST" action="logout.php" style="margin-top:0.6rem">
            <button class="logout-btn">🚪 Se déconnecter</button>
        </form>
    </div>
</aside>

<!-- ══ MAIN ══ -->
<div class="main">
    <div class="topbar">
        <h1>
            <?= [
                'dashboard'=>'📊 Dashboard',
                'formations'=>'📚 Formations',
                'categories'=>'🗂️ Catégories',
                'inscriptions'=>'📝 Inscriptions',
                'etudiants'=>'👥 Étudiants'
            ][$tab] ?? 'Dashboard' ?>
        </h1>
        <span class="topbar-date"><?= date('d/m/Y H:i') ?> · Bienvenue, <?= htmlspecialchars(explode(' ', $_SESSION['user_nom'])[0]) ?></span>
    </div>

    <div class="content">

        <?php if ($msg): ?>
            <div class="alert alert-<?= $msgType ?>"><?= $msg ?></div>
        <?php endif; ?>

        <!-- ════ DASHBOARD ════ -->
        <?php if ($tab === 'dashboard'): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-icon">📚</div>
                <div class="stat-card-val"><?= $nbFormations ?></div>
                <div class="stat-card-lbl">Formations actives</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon">👥</div>
                <div class="stat-card-val"><?= $nbEtudiants ?></div>
                <div class="stat-card-lbl">Étudiants inscrits</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon">📝</div>
                <div class="stat-card-val"><?= $nbInscriptions ?></div>
                <div class="stat-card-lbl">Inscriptions totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon">✅</div>
                <div class="stat-card-val"><?= $nbConfirmees ?></div>
                <div class="stat-card-lbl">Inscriptions confirmées</div>
            </div>
        </div>

        <div class="section-box">
            <div class="section-box-head">
                <h2>Dernières inscriptions</h2>
                <a href="?tab=inscriptions" class="btn btn-ghost btn-sm">Voir tout →</a>
            </div>
            <table class="admin-table">
                <thead><tr><th>Étudiant</th><th>Formation</th><th>Date</th><th>Statut</th></tr></thead>
                <tbody>
                <?php foreach (array_slice($inscriptions, 0, 8) as $i): ?>
                    <tr>
                        <td><?= htmlspecialchars($i['etudiant']) ?></td>
                        <td><?= htmlspecialchars($i['formation']) ?></td>
                        <td><?= date('d/m/Y', strtotime($i['date_inscription'])) ?></td>
                        <td>
                            <?php
                                $bc = ['confirmée'=>'badge-green','en_attente'=>'badge-yellow','annulée'=>'badge-red'];
                                echo "<span class='badge ".($bc[$i['statut']]??'badge-teal')."'>{$i['statut']}</span>";
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- ════ FORMATIONS ════ -->
        <?php elseif ($tab === 'formations'): ?>

        <!-- Formulaire ajout/édition -->
        <div class="section-box" style="margin-bottom:2rem">
            <div class="section-box-head">
                <h2><?= $editFormation ? '✏️ Modifier la formation' : '➕ Ajouter une formation' ?></h2>
            </div>
            <form method="POST" class="admin-form">
                <input type="hidden" name="<?= $editFormation ? 'edit_formation' : 'add_formation' ?>" value="1">
                <?php if ($editFormation): ?><input type="hidden" name="id" value="<?= $editFormation['id'] ?>"><?php endif; ?>
                <div class="form-grid">
                    <div class="fg full">
                        <label>Titre *</label>
                        <input type="text" name="titre" required placeholder="Titre de la formation" value="<?= htmlspecialchars($editFormation['titre'] ?? '') ?>">
                    </div>
                    <div class="fg full">
                        <label>Description</label>
                        <textarea name="description" placeholder="Description..."><?= htmlspecialchars($editFormation['description'] ?? '') ?></textarea>
                    </div>
                    <div class="fg">
                        <label>Durée</label>
                        <input type="text" name="duree" placeholder="ex: 3 mois" value="<?= htmlspecialchars($editFormation['duree'] ?? '') ?>">
                    </div>
                    <div class="fg">
                        <label>Prix (DH)</label>
                        <input type="number" name="prix" step="0.01" min="0" placeholder="0.00" value="<?= $editFormation['prix'] ?? '' ?>">
                    </div>
                    <div class="fg">
                        <label>Places disponibles</label>
                        <input type="number" name="places" min="1" placeholder="20" value="<?= $editFormation['places_disponibles'] ?? '20' ?>">
                    </div>
                    <div class="fg">
                        <label>Niveau</label>
                        <select name="niveau">
                            <?php foreach (['Débutant','Intermédiaire','Avancé'] as $n): ?>
                                <option value="<?= $n ?>" <?= ($editFormation['niveau'] ?? '') === $n ? 'selected' : '' ?>><?= $n ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Catégorie</label>
                        <select name="id_categorie">
                            <option value="">-- Choisir --</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($editFormation['id_categorie'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Date début</label>
                        <input type="date" name="date_debut" value="<?= $editFormation['date_debut'] ?? '' ?>">
                    </div>
                    <div class="fg">
                        <label>Date fin</label>
                        <input type="date" name="date_fin" value="<?= $editFormation['date_fin'] ?? '' ?>">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-teal"><?= $editFormation ? '💾 Enregistrer' : '➕ Ajouter' ?></button>
                    <?php if ($editFormation): ?><a href="?tab=formations" class="btn btn-ghost">Annuler</a><?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Liste formations -->
        <div class="section-box">
            <div class="section-box-head">
                <h2>Liste des formations (<?= $nbFormations ?>)</h2>
            </div>
            <table class="admin-table">
                <thead><tr><th>Titre</th><th>Catégorie</th><th>Prix</th><th>Places</th><th>Niveau</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($formations as $f): ?>
                    <tr>
                        <td><strong style="color:var(--white)"><?= htmlspecialchars($f['titre']) ?></strong><br><small style="color:var(--muted)"><?= $f['duree'] ?></small></td>
                        <td><span class="badge badge-teal"><?= htmlspecialchars($f['cat'] ?? '—') ?></span></td>
                        <td style="color:var(--teal);font-weight:700"><?= number_format($f['prix'],2) ?> DH</td>
                        <td><?= $f['places_disponibles'] ?></td>
                        <td><?php
                            $lv = ['Débutant'=>'badge-green','Intermédiaire'=>'badge-yellow','Avancé'=>'badge-red'];
                            echo "<span class='badge ".($lv[$f['niveau']]??'badge-teal')."'>{$f['niveau']}</span>";
                        ?></td>
                        <td>
                            <a href="?tab=formations&edit=<?= $f['id'] ?>" class="btn btn-ghost btn-sm">✏️ Modifier</a>
                            <form method="POST" style="display:inline" onsubmit="return confirm('Supprimer cette formation ?')">
                                <input type="hidden" name="delete_formation" value="1">
                                <input type="hidden" name="id" value="<?= $f['id'] ?>">
                                <button class="btn btn-red btn-sm">🗑️ Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- ════ CATEGORIES ════ -->
        <?php elseif ($tab === 'categories'): ?>
        <div class="section-box" style="margin-bottom:2rem">
            <div class="section-box-head"><h2>➕ Ajouter une catégorie</h2></div>
            <form method="POST" class="admin-form">
                <input type="hidden" name="add_categorie" value="1">
                <div class="form-grid">
                    <div class="fg"><label>Nom *</label><input type="text" name="nom" required placeholder="Nom de la catégorie"></div>
                    <div class="fg"><label>Description</label><input type="text" name="description" placeholder="Description courte"></div>
                </div>
                <div class="form-actions"><button type="submit" class="btn btn-teal">➕ Ajouter</button></div>
            </form>
        </div>
        <div class="section-box">
            <div class="section-box-head"><h2>Catégories (<?= count($categories) ?>)</h2></div>
            <table class="admin-table">
                <thead><tr><th>#</th><th>Nom</th><th>Description</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach ($categories as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><strong style="color:var(--white)"><?= htmlspecialchars($c['nom']) ?></strong></td>
                    <td style="color:var(--muted)"><?= htmlspecialchars($c['description']) ?></td>
                    <td>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Supprimer cette catégorie ?')">
                            <input type="hidden" name="delete_categorie" value="1">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <button class="btn btn-red btn-sm">🗑️ Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- ════ INSCRIPTIONS ════ -->
        <?php elseif ($tab === 'inscriptions'): ?>
        <div class="section-box">
            <div class="section-box-head"><h2>Inscriptions (<?= $nbInscriptions ?>)</h2></div>
            <table class="admin-table">
                <thead><tr><th>Étudiant</th><th>Formation</th><th>Date</th><th>Statut</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach ($inscriptions as $i): ?>
                <tr>
                    <td><?= htmlspecialchars($i['etudiant']) ?></td>
                    <td><?= htmlspecialchars($i['formation']) ?></td>
                    <td><?= date('d/m/Y', strtotime($i['date_inscription'])) ?></td>
                    <td><?php
                        $bc=['confirmée'=>'badge-green','en_attente'=>'badge-yellow','annulée'=>'badge-red'];
                        echo "<span class='badge ".($bc[$i['statut']]??'badge-teal')."'>{$i['statut']}</span>";
                    ?></td>
                    <td>
                        <form method="POST" style="display:flex;gap:6px;align-items:center">
                            <input type="hidden" name="update_statut" value="1">
                            <input type="hidden" name="id" value="<?= $i['id'] ?>">
                            <select name="statut" class="sel-statut">
                                <option value="en_attente"  <?= $i['statut']==='en_attente'  ?'selected':'' ?>>En attente</option>
                                <option value="confirmée"   <?= $i['statut']==='confirmée'   ?'selected':'' ?>>Confirmée</option>
                                <option value="annulée"     <?= $i['statut']==='annulée'     ?'selected':'' ?>>Annulée</option>
                            </select>
                            <button class="btn btn-teal btn-sm">✔</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- ════ ÉTUDIANTS ════ -->
        <?php elseif ($tab === 'etudiants'): ?>
        <div class="section-box">
            <div class="section-box-head"><h2>Étudiants inscrits (<?= $nbEtudiants ?>)</h2></div>
            <table class="admin-table">
                <thead><tr><th>Nom complet</th><th>Email</th><th>Téléphone</th><th>Date inscription</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach ($etudiants as $u): ?>
                <tr>
                    <td><strong style="color:var(--white)"><?= htmlspecialchars($u['prenom'].' '.$u['nom']) ?></strong></td>
                    <td style="color:var(--teal)"><?= htmlspecialchars($u['email']) ?></td>
                    <td style="color:var(--muted)"><?= htmlspecialchars($u['telephone'] ?: '—') ?></td>
                    <td style="color:var(--muted)"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Supprimer cet étudiant ?')">
                            <input type="hidden" name="delete_user" value="1">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <button class="btn btn-red btn-sm">🗑️ Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div><!-- /content -->
</div><!-- /main -->

</body>
</html>