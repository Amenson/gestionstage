<?php
session_start();
require_once("../config/db.php");

if(!isset($_SESSION["etudiant"])){
    header("Location: ../auth/login.php");
    exit;
}

$idEtud = $_SESSION["etudiant"];

// ======================
// TRAITEMENT POSTULER
// ======================
if(isset($_GET["postuler"])){

    $idStage = (int) $_GET["postuler"];

    // Vérifier si déjà postulé
    $check = $pdo->prepare("SELECT * FROM Candidature WHERE fkEtudiant = ? AND fkStage = ?");
    $check->execute([$idEtud, $idStage]);

    if($check->rowCount() == 0){

        $stmt = $pdo->prepare("
            INSERT INTO Candidature(fkEtudiant, fkStage, dateCandidature)
            VALUES (?, ?, CURDATE())
        ");
        $stmt->execute([$idEtud, $idStage]);

        $msg = "✅ Candidature envoyée avec succès.";
    } else {
        $msg = "⚠️ Tu as déjà postulé à ce stage.";
    }
}

// ======================
// FILTRES
// ======================
$keyword = $_GET["keyword"] ?? "";
$entreprise = $_GET["entreprise"] ?? "";
$type = $_GET["type"] ?? "";

// Listes pour filtres
$entreprises = $pdo->query("SELECT * FROM Entreprise")->fetchAll();
$types = $pdo->query("SELECT * FROM TypeStage")->fetchAll();

// ======================
// CONSTRUCTION REQUÊTE
// ======================
$sql = "
SELECT 
    s.numOffre, s.libStage, s.periodeStage, s.moisStage, s.remunerationStage, s.descStage,
    e.nomEntreprise, t.libTypeStage
FROM Stage s
JOIN Entreprise e ON s.fkEntreprise = e.numSiret
JOIN TypeStage t ON s.fkTypeStage = t.codeTypeStage
WHERE 1=1
";

$params = [];

if (!empty($keyword)) {
    $sql .= " AND (s.libStage LIKE ? OR s.descStage LIKE ?) ";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

if (!empty($entreprise)) {
    $sql .= " AND e.numSiret = ? ";
    $params[] = $entreprise;
}

if (!empty($type)) {
    $sql .= " AND t.codeTypeStage = ? ";
    $params[] = $type;
}

$sql .= " ORDER BY s.dateParution DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$stages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Consulter les stages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

    <h2 class="mb-4 text-center">🎓 Offres de stage</h2>

    <?php if(isset($msg)): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <!-- 🔍 Filtres -->
    <form method="GET" class="row g-3 mb-4">

        <div class="col-md-4">
            <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" class="form-control" placeholder="🔍 Mot-clé...">
        </div>

        <div class="col-md-3">
            <select name="entreprise" class="form-select">
                <option value="">-- Toutes les entreprises --</option>
                <?php foreach($entreprises as $e): ?>
                    <option value="<?= $e["numSiret"] ?>" <?= ($entreprise==$e["numSiret"])?"selected":"" ?>>
                        <?= htmlspecialchars($e["nomEntreprise"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value="">-- Tous les types --</option>
                <?php foreach($types as $t): ?>
                    <option value="<?= $t["codeTypeStage"] ?>" <?= ($type==$t["codeTypeStage"])?"selected":"" ?>>
                        <?= htmlspecialchars($t["libTypeStage"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2 d-grid">
            <button class="btn btn-primary">Filtrer</button>
        </div>

    </form>

    <!-- 📦 Résultats -->
    <div class="row">

    <?php if(count($stages) == 0): ?>
        <div class="alert alert-warning">Aucun stage trouvé.</div>
    <?php endif; ?>

    <?php foreach($stages as $s): ?>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">

                    <h5 class="card-title"><?= htmlspecialchars($s["libStage"]) ?></h5>

                    <p class="text-muted">
                        🏢 <?= htmlspecialchars($s["nomEntreprise"]) ?> |
                        🏷️ <?= htmlspecialchars($s["libTypeStage"]) ?>
                    </p>

                    <p>
                        ⏱️ <?= htmlspecialchars($s["moisStage"]) ?> mois |
                        💰 <?= htmlspecialchars($s["remunerationStage"]) ?>
                    </p>

                    <p>
                        <?= nl2br(htmlspecialchars(substr($s["descStage"],0,200))) ?>...
                    </p>

                    <!-- Compétences -->
                    <p><b>Compétences requises :</b></p>
                    <ul>
                        <?php
                        $stmt2 = $pdo->prepare("
                            SELECT c.libCompet
                            FROM Exiger e
                            JOIN Competence c ON e.codeCompet = c.codeCompet
                            WHERE e.numOffre = ?
                        ");
                        $stmt2->execute([$s["numOffre"]]);
                        foreach($stmt2->fetchAll() as $c):
                        ?>
                            <li><?= htmlspecialchars($c["libCompet"]) ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- 🔘 POSTULER -->
                    <div class="d-grid mt-3">
                        <a class="btn btn-success"
                           href="?postuler=<?= $s["numOffre"] ?>"
                           onclick="return confirm('Voulez-vous postuler à ce stage ?')">
                           📩 Postuler
                        </a>
                    </div>

                </div>
            </div>
        </div>

    <?php endforeach; ?>

    </div>

    <a href="dashboard.php" class="btn btn-secondary mt-3">⬅ Retour</a>

</div>

</body>
</html>
