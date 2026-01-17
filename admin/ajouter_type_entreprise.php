<?php
require_once("../includes/security.php");
require_once("../config/db.php");
require_once("../includes/csrf.php");
require_once("../includes/helpers.php");

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lib = trim($_POST['libelle'] ?? '');
    $token = $_POST['csrf_token'] ?? '';

    if ($lib === '') {
        $message = 'Le libellé est requis.';
    } elseif (!csrf_validate($token)) {
        $message = 'Token CSRF invalide.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO TypeEntreprise(libEntr) VALUES (?)");
        $stmt->execute([$lib]);
        $message = "Type d'entreprise ajouté avec succès !";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="h4 mb-0"><i class="bi bi-plus-circle"></i> Ajouter un Type d'Entreprise</h2>
        </div>
        <div class="card-body">
            <?php if($message): ?>
                <div class="alert alert-success text-center" role="alert">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="typeEntrepriseForm">
                <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
                <div class="mb-3">
                    <label for="libelle" class="form-label">Libellé</label>
                    <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Libellé" required>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
:root {
    --primary-blue: #0d6efd;
    --secondary-orange: #fd7e14;
    --bg-light: #f8f9fa;
    --card-bg: #ffffff;
    --text-dark: #212529;
}

body {
    background-color: var(--bg-light);
    color: var(--text-dark);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.card {
    border-radius: 12px;
    transition: all 0.3s ease;
}

.card-body .form-control {
    border-radius: 6px;
    border: 1px solid #ced4da;
    transition: all 0.2s;
}

.card-body .form-control:focus {
    border-color: var(--primary-blue);
    box-shadow: 0 0 5px rgba(13,110,253,0.25);
    outline: none;
}

.btn-primary {
    background-color: var(--primary-blue);
    border-color: var(--primary-blue);
    transition: all 0.3s;
}

.btn-primary:hover {
    background-color: var(--secondary-orange);
    border-color: var(--secondary-orange);
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

.alert-success {
    border-radius: 6px;
}
</style>

<?php include '../includes/footer.php'; ?>
