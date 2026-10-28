<?php

session_start();
require_once("../config/db.php");

if(!isset($_SESSION['admin'])) {
    header("Location: ../authentification/login_admin.php");
    exit();
}

/* =========================
   ENVOI NOTIFICATION
========================= */
$notif_message = "";
$notif_erreur  = "";

if(isset($_POST['envoyer_notif'])) {

    $id_parent = intval($_POST['id_parent']);
    $message   = trim($_POST['message']);
    $type      = trim($_POST['type']);
    $id_admin  = $_SESSION['admin'];

    if(!empty($message) && $id_parent > 0) {

        $sql = "INSERT INTO notification 
                    (date_envoi, message, type, lu, id_admin, id_parent)
                VALUES (NOW(), ?, ?, 0, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$message, $type, $id_admin, $id_parent]);

        $notif_message = "Notification envoyée avec succès !";

    } else {
        $notif_erreur = "Veuillez remplir tous les champs.";
    }
}

/* =========================
   STATISTIQUES
========================= */

$nbEleves  = $pdo->query("SELECT COUNT(*) FROM eleve")->fetchColumn();
$nbParents = $pdo->query("SELECT COUNT(*) FROM parent")->fetchColumn();
$nbNotes   = $pdo->query("SELECT COUNT(*) FROM notes")->fetchColumn();

/* =========================
   LISTE PARENTS
========================= */

$parents = $pdo->query("SELECT id_parent, nom_parent, prenom_parent FROM parent ORDER BY nom_parent")->fetchAll();

/* =========================
   ÉTAT DES ÉLÈVES
========================= */

$sql = "SELECT 
            e.id_eleve,
            e.nom_eleve,
            e.prenom_eleve,
            e.classe_eleve,
            p.nom_parent,

            SUM(n.valeur * m.coefficient) / SUM(m.coefficient) AS moyenne

        FROM eleve e

        JOIN parent p 
        ON e.id_parent = p.id_parent

        LEFT JOIN notes n 
        ON e.id_eleve = n.id_eleve

        LEFT JOIN matiere m 
        ON n.id_matiere = m.id_matiere

        GROUP BY e.id_eleve";

$eleves = $pdo->query($sql)->fetchAll();

/* =========================
   NOTIFICATIONS
========================= */

$sql2 = "SELECT 
            n.message,
            n.date_envoi,
            p.nom_parent,
            p.prenom_parent

        FROM notification n

        JOIN parent p
        ON n.id_parent = p.id_parent

        ORDER BY n.date_envoi DESC";

$notifs = $pdo->query($sql2)->fetchAll();

/* =========================
   COMPTEURS RISQUE
========================= */

$stable = 0;
$difficulte = 0;
$risque = 0;

foreach($eleves as $e){

    $m = $e['moyenne'];

    if($m === null){
        continue;
    }

    if($m >= 12){
        $stable++;
    }
    elseif($m >= 8){
        $difficulte++;
    }
    else{
        $risque++;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>EduConnect - Dashboard Administrateur</title>

    <link rel="stylesheet" href="style_admin.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="sidebar">

    <div class="logo">
        <img src="logo.png"
             alt="EduConnect Logo">
    </div>

    <ul>
        <li class="active">
            <i class="fa fa-gauge"></i>
            Tableau de bord
        </li>

        <li onclick="openPopup('eleves')">
            <i class="fa fa-users"></i>
            Élèves
        </li>

        <li onclick="openPopup('parents')">
            <i class="fa fa-user-group"></i>
            Parents
        </li>

        <li onclick="openPopup('notes')">
            <i class="fa fa-square-poll-vertical"></i>
            Notes
        </li>

        <li onclick="openPopup('absences')">
            <i class="fa fa-clock"></i>
            Absences
        </li>

        <li onclick="openPopup('notifications')">
            <i class="fa fa-bell"></i>
            Notifications
        </li>

        <li onclick="openPopup('utilisateurs')">
            <i class="fa fa-user-gear"></i>
            Utilisateurs
        </li>

        <li onclick="openPopup('parametres')">
            <i class="fa fa-gear"></i>
            Paramètres
        </li>

        <li onclick="openPopup('rapports')">
            <i class="fa fa-chart-line"></i>
            Rapports
        </li>
    </ul>
</div>

<div class="main">

    <div class="topbar">

        <div>
            <h1>Dashboard Administrateur</h1>
            <p>Bienvenue, Administrateur</p>
        </div>

        <div class="admin-profile">

            <i class="fa fa-user-circle"></i>

            <span>Administrateur</span>

            <i class="fa fa-angle-down"></i>

            <a href="../authentification/logout.php">
                <i class="fa fa-right-from-bracket"></i>
                Déconnexion
            </a>

        </div>
    </div>

    <div class="content">

        <!-- STATISTIQUES -->
        <div class="stats">

            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="fa fa-users"></i>
                </div>
                <div>
                    <p>Nombre total d'élèves</p>
                    <h2><?= $nbEleves ?></h2>
                    <span>Voir les détails <i class="fa fa-angle-right"></i></span>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-icon">
                    <i class="fa fa-user-group"></i>
                </div>
                <div>
                    <p>Nombre de parents</p>
                    <h2><?= $nbParents ?></h2>
                    <span>Voir les détails <i class="fa fa-angle-right"></i></span>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="stat-icon">
                    <i class="fa fa-square-poll-vertical"></i>
                </div>
                <div>
                    <p>Nombre de notes</p>
                    <h2><?= $nbNotes ?></h2>
                    <span>Voir les détails <i class="fa fa-angle-right"></i></span>
                </div>
            </div>

        </div>

        <div class="dashboard-grid">

            <!-- RISQUE -->
            <div class="panel risk-panel">

                <h3>
                    IDS - Répartition des élèves par niveau de risque
                </h3>

                <div class="risk-content">

                    <div class="chart-box">
                        <canvas id="riskChart"></canvas>
                    </div>

                    <div class="risk-list">

                        <div class="risk-item stable">
                            <div>
                                <h4>Élèves stables</h4>
                                <strong><?= $stable ?></strong>
                            </div>
                        </div>

                        <div class="risk-item difficult">
                            <div>
                                <h4>Élèves en difficulté</h4>
                                <strong><?= $difficulte ?></strong>
                            </div>
                        </div>

                        <div class="risk-item danger">
                            <div>
                                <h4>Élèves en risque</h4>
                                <strong><?= $risque ?></strong>
                            </div>
                        </div>

                    </div>
                </div>

                <p class="update">Mise à jour automatique</p>
            </div>

            <!-- NOTIFICATIONS RÉCENTES -->
            <div class="panel alerts-panel">

                <h3>
                    <i class="fa fa-bell"></i>
                    Notifications récentes
                </h3>

                <?php foreach($notifs as $n): ?>

                <div class="alert-item blue-alert">

                    <i class="fa fa-bell"></i>

                    <div>
                        <h4>
                            <?= htmlspecialchars($n['nom_parent']) ?>
                            <?= htmlspecialchars($n['prenom_parent']) ?>
                        </h4>
                        <p><?= htmlspecialchars($n['message']) ?></p>
                    </div>

                    <span><?= $n['date_envoi'] ?></span>

                </div>

                <?php endforeach; ?>

            </div>

            <!-- TABLEAU ÉLÈVES EN DIFFICULTÉ -->
            <div class="panel">

                <h3>
                    <i class="fa fa-users"></i>
                    Élèves en difficulté
                </h3>

                <table>

                    <tr>
                        <th>#</th>
                        <th>Élève</th>
                        <th>Classe</th>
                        <th>Moyenne</th>
                        <th>Niveau</th>
                    </tr>

                    <?php $i = 1; ?>

                    <?php foreach($eleves as $e): ?>

                    <?php
                    $moyenne = $e['moyenne'];
                    if($moyenne !== null && $moyenne < 12):
                    ?>

                    <tr>
                        <td><?= $i++ ?></td>
                        <td>
                            <?= htmlspecialchars($e['nom_eleve']) ?>
                            <?= htmlspecialchars($e['prenom_eleve']) ?>
                        </td>
                        <td><?= htmlspecialchars($e['classe_eleve']) ?></td>
                        <td><?= round($moyenne, 2) ?></td>
                        <td class="dot <?= $moyenne < 8 ? 'red-dot' : 'orange-dot' ?>"></td>
                    </tr>

                    <?php endif; ?>
                    <?php endforeach; ?>

                </table>

            </div>

            <!-- ACTIONS RAPIDES -->
            <div class="panel actions-panel">

                <h3>
                    <i class="fa fa-sliders"></i>
                    Actions rapides
                </h3>

                <div class="actions">

                    <button class="purple-btn"
                            onclick="openPopup('envoyer-notif')">
                        <i class="fa fa-envelope"></i>
                        Envoyer notification
                    </button>

                    <button class="report-btn"
                            onclick="openPopup('rapport')">
                        <i class="fa fa-chart-column"></i>
                        Générer un rapport
                    </button>

                </div>
            </div>

        </div>
    </div>

    <footer>
        © 2026 EduConnect - Tous droits réservés
    </footer>
</div>

<!-- POPUP -->
<div id="popup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <h2 id="popup-title"></h2>
        <div id="popup-body"></div>
    </div>
</div>

<!-- VARIABLES CHART JS + PARENTS + MESSAGES -->
<script>

const stable     = <?= $stable ?>;
const difficulte = <?= $difficulte ?>;
const risque     = <?= $risque ?>;

const parents = <?= json_encode(array_map(function($p) {
    return [
        'id'  => $p['id_parent'],
        'nom' => $p['nom_parent'] . ' ' . $p['prenom_parent']
    ];
}, $parents)) ?>;

const notifSuccess = <?= json_encode($notif_message) ?>;
const notifErreur  = <?= json_encode($notif_erreur) ?>;

</script>

<script src="scriptadmin.js"></script>

</body>
</html>