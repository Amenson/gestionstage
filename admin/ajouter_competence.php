<?php
require_once("../includes/security.php");
require_once("../config/db.php");
require_once("../includes/csrf.php");
require_once("../includes/helpers.php");

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = trim($_POST['type'] ?? '');
    $lib = trim($_POST['libelle'] ?? '');
    $token = $_POST['csrf_token'] ?? '';

    if ($type === '' || $lib === '') {
        $message = 'Tous les champs sont requis.';
    } elseif (!csrf_validate($token)) {
        $message = 'Token CSRF invalide.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO Competence(typeCompet, libCompet) VALUES (:type, :lib)');
        $stmt->execute([':type' => $type, ':lib' => $lib]);
        $message = 'Compétence ajoutée avec succès.';
    }
}

include '../includes/header.php';
?>

<div class="container my-5" style="max-width:720px;">
    <div class="card shadow-sm p-4">
        <h2 class="h5 mb-3"><i class="bi bi-plus-circle"></i> Ajouter une Compétence</h2>

        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'succ') !== false ? 'alert-success' : 'alert-danger' ?>"><?= esc($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">

            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <input id="type" name="type" class="form-control" placeholder="Technique, SoftSkill..." required>
            </div>

            <div class="mb-3">
                <label for="libelle" class="form-label">Libellé</label>
                <input id="libelle" name="libelle" class="form-control" placeholder="Libellé" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
                <button class="btn btn-primary" type="submit"><i class="bi bi-plus-circle"></i> Ajouter</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>