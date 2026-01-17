<?php
require_once("../includes/security.php");
require_once("../config/db.php");
require_once("../includes/csrf.php");
require_once("../includes/helpers.php");

$stages = $pdo->query("SELECT * FROM Stage WHERE fkEtudiant IS NOT NULL")->fetchAll();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $stageId = (int)($_POST['stage'] ?? 0);
    $critere = trim($_POST['critere'] ?? '');
    $note = filter_var($_POST['note'] ?? '', FILTER_VALIDATE_INT);

    if (!csrf_validate($token)) {
        $message = 'Token CSRF invalide.';
    } elseif ($stageId <= 0 || $critere === '' || $note === false || $note < 0 || $note > 20) {
        $message = 'Données invalides.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO NoteStage(numOffre, numCritere, noteStage) VALUES (?,?,?)");
        $stmt->execute([$stageId, $critere, $note]);
        $message = 'Note ajoutée avec succès.';
    }
}

include '../includes/header.php';
?>

<div class="container my-4" style="max-width:720px;">
    <div class="card shadow-sm p-4">
        <h2 class="h5 mb-3"><i class="bi bi-pencil-square"></i> Noter un Stage</h2>

        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'succ') !== false ? 'alert-success' : 'alert-danger' ?>"><?= esc($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">

            <div class="mb-3">
                <label for="stage" class="form-label">Stage</label>
                <select id="stage" name="stage" class="form-select" required>
                    <?php foreach($stages as $s): ?>
                        <option value="<?= esc($s['numOffre']) ?>"><?= esc($s['libStage']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="critere" class="form-label">Critère</label>
                <input id="critere" name="critere" class="form-control" placeholder="Rapport, Soutenance..." required>
            </div>

            <div class="mb-3">
                <label for="note" class="form-label">Note</label>
                <input id="note" type="number" name="note" class="form-control" min="0" max="20" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
                <button class="btn btn-primary" type="submit">Noter</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
