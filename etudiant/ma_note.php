<?php
session_start();   // ✅ OBLIGATOIRE
require_once("../config/db.php");

// Vérifier si l'étudiant est connecté
if(!isset($_SESSION["idEtudiant"])){
    header("Location: ../auth/login_etudiant.php");
    exit;
}

$idEtud = $_SESSION["idEtudiant"];

// Récupérer le stage de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM Stage WHERE fkEtudiant = ?");
$stmt->execute([$idEtud]);
$stage = $stmt->fetch();

if(!$stage){
    echo "Aucun stage affecté";
    exit;
}

// Récupérer les notes
$stmt2 = $pdo->prepare("SELECT * FROM NoteStage WHERE numOffre = ?");
$stmt2->execute([$stage["numOffre"]]);
$notes = $stmt2->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ma note</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

<h3>📊 Notes de mon stage : <?= htmlspecialchars($stage["libStage"]) ?></h3>

<table class="table table-bordered table-striped mt-3">
<tr>
    <th>Critère</th>
    <th>Note</th>
</tr>

<?php
$total = 0;
$count = 0;

foreach($notes as $n):
    $total += $n["noteStage"];
    $count++;
?>
<tr>
    <td><?= htmlspecialchars($n["numCritere"]) ?></td>
    <td><?= htmlspecialchars($n["noteStage"]) ?>/20</td>
</tr>
<?php endforeach; ?>
</table>

<h4>✅ Moyenne : <?= $count ? round($total / $count, 2) : 0 ?>/20</h4>

<a href="dashboard.php" class="btn btn-secondary">⬅ Retour</a>

</div>
</body>
</html>
