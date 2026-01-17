<?php
require_once("../includes/security.php");
require_once("../config/db.php");
require_once("../includes/csrf.php");
require_once("../includes/helpers.php");

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lib = trim($_POST['libelle'] ?? '');
    $token = $_POST['csrf_token'] ?? '';

    if ($lib === '') {
        $message = 'Le nom du pays est requis.';
    } elseif (!csrf_validate($token)) {
        $message = 'Token CSRF invalide.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO Pays(libPays) VALUES (:lib)');
        $stmt->execute([':lib' => $lib]);
        $message = 'Pays ajouté avec succès.';
    }
}

include '../includes/header.php';
?>

<div class="container my-5" style="max-width:560px;">
    <div class="card shadow-sm p-4">
        <h2 class="h5 mb-3"><i class="bi bi-plus-circle"></i> Ajouter un Pays</h2>

        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'succ') !== false ? 'alert-success' : 'alert-danger' ?>"><?= esc($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
            <div class="mb-3">
                <input name="libelle" class="form-control" placeholder="Nom du pays" required>
            </div>
            <div class="d-flex justify-content-between">
                <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
                <button class="btn btn-primary" type="submit">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
