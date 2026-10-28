<?php

session_start();
require_once("../config/db.php");
require_once("../dompdf/autoload.inc.php");

use Dompdf\Dompdf;

if(!isset($_SESSION['parent'])) {
    header("Location: ../authentification/login_parent.php");
    exit();
}

$id_eleve = $_GET['id'];

/* élève */
$sql = "SELECT * FROM eleve WHERE id_eleve = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_eleve]);
$eleve = $stmt->fetch();

/* notes */
$sql2 = "SELECT n.valeur, m.nom_matiere, m.coefficient
         FROM notes n
         JOIN matiere m ON n.id_matiere = m.id_matiere
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

$moyenne = ($coef != 0) ? $total / $coef : 0;

/* état */
if($moyenne >= 12){
    $etat = "Bon";
    $color = "green";
}
elseif($moyenne >= 8){
    $etat = "Difficulté";
    $color = "orange";
}
else{
    $etat = "Risque";
    $color = "red";
}

/* HTML BULLETIN */
$html = "
<h1 style='text-align:center;'>Bulletin Scolaire</h1>
<hr>

<h3>Élève : {$eleve['nom_eleve']} {$eleve['prenom_eleve']}</h3>
<p>Classe : {$eleve['classe_eleve']}</p>

<h3>Notes</h3>

<table border='1' width='100%' cellpadding='5'>
<tr>
    <th>Matière</th>
    <th>Note</th>
    <th>Coefficient</th>
</tr>
";

foreach($notes as $n){
    $html .= "
    <tr>
        <td>{$n['nom_matiere']}</td>
        <td>{$n['valeur']}</td>
        <td>{$n['coefficient']}</td>
    </tr>
    ";
}

$html .= "
</table>

<h3>Moyenne générale : " . round($moyenne,2) . "</h3>

<h3 style='color:$color'>État : $etat</h3>
";

/* DOMPDF */
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();
$dompdf->stream("bulletin_" . $eleve['nom_eleve'] . ".pdf", ["Attachment" => true]);

?>