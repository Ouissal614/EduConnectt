<?php

session_start();
require_once("../config/db.php");

if(!isset($_SESSION['admin'])) {
    header("Location: ../authentification/login_admin.php");
    exit();
}

$parents = $pdo->query("SELECT * FROM parent")->fetchAll();

if(isset($_POST['envoyer'])) {

    $id_parent = $_POST['id_parent'];
    $message = $_POST['message'];

    $sql = "INSERT INTO notification (date_envoi, message, type, lu, id_admin, id_parent)
            VALUES (NOW(), ?, 'info', 0, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $message,
        $_SESSION['admin'],
        $id_parent
    ]);

    echo "Notification envoyée";
}

?>

<h2>Envoyer Notification</h2>

<form method="POST">

<select name="id_parent">

<?php foreach($parents as $p): ?>
<option value="<?= $p['id_parent'] ?>">
    <?= $p['nom_parent'] ?>
</option>
<?php endforeach; ?>

</select>

<br><br>

<textarea name="message" required></textarea>

<br><br>

<button type="submit" name="envoyer">Envoyer</button>

</form>