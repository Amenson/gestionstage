<?php
require_once("../includes/security.php");
require_once("../config/db.php");
require_once("../includes/csrf.php");
require_once("../includes/helpers.php");

$stageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($stageId <= 0) {
    header('Location: dashboard.php');
    exit;
}

$competences = $pdo->query("SELECT * FROM Competence ORDER BY libCompet")->fetchAll();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!csrf_validate($token)) {
        $message = 'Token CSRF invalide.';
    } elseif (empty($_POST['competences']) || !is_array($_POST['competences'])) {
        $message = 'Veuillez sélectionner au moins une compétence.';
    } else {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO Exiger(numOffre, codeCompet, degreMaitrise) VALUES (?, ?, ?)");
            foreach ($_POST['competences'] as $comp) {
                if (!is_numeric($comp)) continue;
                $stmt->execute([$stageId, (int)$comp, 'Bon']);
            }
            $pdo->commit();
            $message = 'Compétences associées avec succès.';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = 'Erreur lors de l\'association : ' . $e->getMessage();
        }
    }
}

include '../includes/header.php';
?>

<div class="container my-4" style="max-width:720px;">
    <div class="card shadow-sm p-4">
        <h2 class="h5 mb-3"><i class="bi bi-link-45deg"></i> Associer des compétences au stage #<?= esc($stageId) ?></h2>

        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'succ') !== false ? 'alert-success' : 'alert-danger' ?>"><?= esc($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
            <div class="mb-3">
                <?php foreach($competences as $c): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="competences[]" value="<?= esc($c['codeCompet']) ?>" id="comp<?= esc($c['codeCompet']) ?>">
                        <label class="form-check-label" for="comp<?= esc($c['codeCompet']) ?>"><?= esc($c['libCompet']) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="d-flex justify-content-between">
                <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
                <button class="btn btn-primary" type="submit">Associer</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
