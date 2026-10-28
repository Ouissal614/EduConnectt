<?php

session_start();
require_once("../config/db.php");

if(!isset($_SESSION['parent'])) {
    header("Location: ../authentification/login_parent.php");
    exit();
}

$id_parent = $_SESSION['parent'];

/* élèves du parent */
$sql = "SELECT * FROM eleve WHERE id_parent = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_parent]);
$eleves = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EduConnect - Espace Parent</title>

    <link rel="stylesheet" href="style_parent.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="sidebar-logo">
        <img src="logo.png" alt="Logo">
    </div>

    <ul>
        <li class="active">
            <i class="fa fa-home"></i>
            Tableau de bord
        </li>

        <li onclick="openPopup('notes')">
            <i class="fa fa-chart-bar"></i>
            Notes
        </li>

        <li onclick="openPopup('absences')">
            <i class="fa fa-calendar"></i>
            Absences
        </li>

        <li onclick="openPopup('notifications')">
            <i class="fa fa-bell"></i>
            Notifications
        </li>

        <li onclick="openPopup('profil')">
            <i class="fa fa-user"></i>
            Profil
        </li>

        <li onclick="openPopup('parametres')">
            <i class="fa fa-cog"></i>
            Paramètres
        </li>

        <li>
            <a href="../authentification/logout.php" class="logout-link">
                <i class="fa fa-sign-out-alt"></i>
                Déconnexion
            </a>
        </li>
    </ul>

</div>

<!-- MAIN -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">

        <h1>EduConnect - Espace Parent</h1>

        <div class="user">
            <i class="fa fa-bell"></i>
            <i class="fa fa-user-circle avatar"></i>
            <span>Bonjour Parent</span>
        </div>

    </div>

    <!-- CONTENT -->
    <div class="content">

        <?php foreach($eleves as $e): ?>

        <?php

        $id_eleve = $e['id_eleve'];

        /* notes */
        $sql2 = "SELECT 
                    n.valeur,
                    m.nom_matiere,
                    m.coefficient
                FROM notes n
                JOIN matiere m 
                ON n.id_matiere = m.id_matiere
                WHERE n.id_eleve = ?";

        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([$id_eleve]);
        $notes = $stmt2->fetchAll();

        /* moyenne */
        $total = 0;
        $coef = 0;

        foreach($notes as $n){
            $total += $n['valeur'] * $n['coefficient'];
            $coef += $n['coefficient'];
        }

        $moyenne = ($coef != 0) ? $total / $coef : null;

        /* état */
        if($moyenne === null){
            $etat = "Pas de notes";
            $color = "gray";
            $statusClass = "orange-bg";
        }
        elseif($moyenne >= 12){
            $etat = "Bon";
            $color = "green";
            $statusClass = "green-bg";
        }
        elseif($moyenne >= 8){
            $etat = "Difficulté";
            $color = "orange";
            $statusClass = "orange-bg";
        }
        else{
            $etat = "Risque";
            $color = "red";
            $statusClass = "red-bg";
        }

        /* notes > 10 */
        $sup10 = 0;

        foreach($notes as $n){
            if($n['valeur'] >= 10){
                $sup10++;
            }
        }

        $pourcentage =
            count($notes) > 0
            ? round(($sup10 / count($notes)) * 100)
            : 0;

        ?>

        <!-- TITRE ELEVE -->
        <h2>
            Élève :
            <?= $e['nom_eleve'] ?>
            <?= $e['prenom_eleve'] ?>
        </h2>

        <!-- TABLE -->
        <div class="table-box">

            <table>

                <thead>
                    <tr>
                        <th>Matière</th>
                        <th>Note</th>
                        <th>Coeff</th>
                        <th>Statut</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach($notes as $n): ?>

                    <?php

                    if($n['valeur'] >= 12){
                        $noteClass = "green";
                        $status = "green-bg";
                    }
                    elseif($n['valeur'] >= 8){
                        $noteClass = "orange";
                        $status = "orange-bg";
                    }
                    else{
                        $noteClass = "red";
                        $status = "red-bg";
                    }

                    ?>

                    <tr>

                        <td>
                            <?= $n['nom_matiere'] ?>
                        </td>

                        <td class="note <?= $noteClass ?>">
                            <?= $n['valeur'] ?>
                        </td>

                        <td>
                            <?= $n['coefficient'] ?>
                        </td>

                        <td>
                            <span class="status <?= $status ?>"></span>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <!-- CARDS -->
        <div class="cards">

            <!-- MOYENNE -->
            <div class="card">

                <div class="icon-box blue-light">
                    <i class="fa fa-file-alt"></i>
                </div>

                <div>
                    <p>Moyenne générale</p>

                    <h3>
                        <?= $moyenne !== null ? round($moyenne,2) . " / 20" : "—" ?>
                    </h3>
                </div>

            </div>

            <!-- NOTES > 10 -->
            <div class="card">

                <div class="icon-box green-light">
                    <i class="fa fa-chart-line"></i>
                </div>

                <div>
                    <p>Notes au-dessus de 10</p>

                    <h3 class="green">
                        <?= $pourcentage ?>%
                    </h3>
                </div>

            </div>

            <!-- ETAT -->
            <div class="card">

                <div class="icon-box orange-light">
                    <i class="fa fa-circle-info"></i>
                </div>

                <div>
                    <p>État de l'élève</p>

                    <h3 class="<?= $color ?>">
                        <?= $etat ?>
                    </h3>
                </div>

            </div>

        </div>

        <!-- BOUTON BULLETIN -->
        <div class="bulletin-box">

            <a class="btn-bulletin"
               href="bulletin.php?id=<?= $id_eleve ?>">

                <i class="fa fa-file-pdf"></i>
                Télécharger bulletin PDF

            </a>

        </div>

        <hr class="separator">

        <?php endforeach; ?>

    </div>

</div>

<!-- POPUP -->
<div id="popup" class="popup">

    <div class="popup-content">

        <span class="close" onclick="closePopup()">
            &times;
        </span>

        <h2 id="popup-title"></h2>

        <div id="popup-body"></div>

    </div>

</div>

<script src="scriptparent.js"></script>

</body>
</html>