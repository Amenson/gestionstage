<?php
require_once("../includes/security.php");
require_once("../config/db.php");

// =====================
// TRAITEMENT ACTIONS
// =====================
if(isset($_GET["accepter"])){

    $idCand = (int) $_GET["accepter"];

    // Récupérer la candidature
    $stmt = $pdo->prepare("SELECT * FROM Candidature WHERE idCandidature = ?");
    $stmt->execute([$idCand]);
    $cand = $stmt->fetch();

    if($cand){
        // Accepter candidature
        $pdo->prepare("UPDATE Candidature SET statut='Acceptée' WHERE idCandidature=?")->execute([$idCand]);

        // Affecter le stage à l'étudiant
        $pdo->prepare("UPDATE Stage SET fkEtudiant=? WHERE numOffre=?")
            ->execute([$cand["fkEtudiant"], $cand["fkStage"]]);

        $msg = "✅ Candidature acceptée et stage affecté.";
    }
}

if(isset($_GET["refuser"])){

    $idCand = (int) $_GET["refuser"];
    $pdo->prepare("UPDATE Candidature SET statut='Refusée' WHERE idCandidature=?")->execute([$idCand]);
    $msg = "❌ Candidature refusée.";
}

// =====================
// LISTE DES CANDIDATURES
// =====================
$cands = $pdo->query("
    SELECT c.*, e.nomEtud, e.prenomEtud, s.libStage
    FROM Candidature c
    JOIN Etudiant e ON c.fkEtudiant = e.numEtud
    JOIN Stage s ON c.fkStage = s.numOffre
    ORDER BY c.dateCandidature DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des candidatures</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

<h3>📨 Gestion des candidatures</h3>

<?php if(isset($msg)): ?>
    <div class="alert alert-info"><?= $msg ?></div>
<?php endif; ?>

<table class="table table-bordered table-striped">
<tr>
    <th>Étudiant</th>
    <th>Stage</th>
    <th>Date</th>
    <th>Statut</th>
    <th>Actions</th>
</tr>

<?php foreach($cands as $c): ?>
<tr>
    <td><?= htmlspecialchars($c["nomEtud"]." ".$c["prenomEtud"]) ?></td>
    <td><?= htmlspecialchars($c["libStage"]) ?></td>
    <td><?= htmlspecialchars($c["dateCandidature"]) ?></td>
    <td>
        <span class="badge 
            <?= $c["statut"]=="Acceptée" ? "bg-success" : ($c["statut"]=="Refusée" ? "bg-danger" : "bg-warning") ?>">
            <?= htmlspecialchars($c["statut"]) ?>
        </span>
    </td>
    <td>
        <?php if($c["statut"] == "En attente"): ?>
            <a class="btn btn-success btn-sm"
               href="?accepter=<?= $c["idCandidature"] ?>"
               onclick="return confirm('Accepter cette candidature ?')">
               ✅ Accepter
            </a>

            <a class="btn btn-danger btn-sm"
               href="?refuser=<?= $c["idCandidature"] ?>"
               onclick="return confirm('Refuser cette candidature ?')">
               ❌ Refuser
            </a>
        <?php else: ?>
            ---
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

<a href="dashboard.php" class="btn btn-secondary">⬅ Retour</a>

</div>

</body>
</html>
