<?php
require_once("../includes/security.php");
require_once("../config/db.php");
require_once("../includes/csrf.php");
require_once("../includes/helpers.php");

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    $libelle = trim($_POST['libelle'] ?? '');
    $duree = trim($_POST['duree'] ?? '');
    $token = $_POST['csrf_token'] ?? '';

    if ($libelle === '') {
        $errors[] = 'Le libellé est requis.';
    }
    if ($duree === '') {
        $errors[] = 'La durée est requise.';
    }
    if (!csrf_validate($token)) {
        $errors[] = 'Token CSRF invalide.';
    }

    if (!empty($errors)) {
        if ($isAjax) {
            json_response(['status' => 'error', 'errors' => $errors], 422);
        } else {
            $message = implode(' ', $errors);
        }
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO TypeStage (libStage, duree) VALUES (:lib, :duree)');
            $stmt->execute([':lib' => $libelle, ':duree' => $duree]);

            if ($isAjax) {
                json_response(['status' => 'ok', 'message' => 'Type de stage ajouté avec succès.']);
            } else {
                $message = 'Type de stage ajouté avec succès.';
            }
        } catch (PDOException $e) {
            if ($isAjax) {
                json_response(['status' => 'error', 'errors' => ['Erreur BD : ' . $e->getMessage()]], 500);
            } else {
                $message = 'Erreur lors de l\'enregistrement : ' . $e->getMessage();
            }
        }
    }
}

// Render view below
?>

<?php include '../includes/header.php'; ?>

<div class="container my-5">
    <div class="card shadow-sm p-4 mx-auto" style="max-width:480px;">
        <h2 class="h4 text-center text-primary mb-3"><i class="bi bi-plus-circle"></i> Ajouter un Type de Stage</h2>

        <?php if ($message): ?>
            <div class="alert <?= empty($errors) ? 'alert-success' : 'alert-danger' ?>" role="alert">
                <?= esc($message) ?>
            </div>
        <?php endif; ?>

        <form id="typeStageForm" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">

            <div class="mb-3">
                <label for="libelle" class="form-label">Libellé</label>
                <input id="libelle" name="libelle" class="form-control" placeholder="Libellé" required>
            </div>

            <div class="mb-3">
                <label for="duree" class="form-label">Durée</label>
                <input id="duree" name="duree" class="form-control" placeholder="Ex: 3 mois" required>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Ajouter</button>
            </div>
        </form>

        <div id="feedback" class="mt-3" aria-live="polite"></div>
    </div>
</div>

<style>
:root{--primary-blue:#0d6efd;--secondary-orange:#fd7e14;--bg-light:#f8f9fa;--card-bg:#fff}
body{background-color:var(--bg-light)}
.card{border-radius:12px}
.form-control:focus{border-color:var(--primary-blue);box-shadow:0 0 6px rgba(13,110,253,0.15)}
.btn-primary:hover{background-color:var(--secondary-orange);border-color:var(--secondary-orange)}
</style>

<script>
(function(){
    const form = document.getElementById('typeStageForm');
    const feedback = document.getElementById('feedback');

    form.addEventListener('submit', function(e){
        e.preventDefault();

        const data = new FormData(form);

        fetch('', {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: data
        })
        .then(r => r.json())
        .then(json => {
            if (json.status === 'ok') {
                feedback.innerHTML = '<div class="alert alert-success">' + json.message + '</div>';
                form.reset();
                window.scrollTo({top: feedback.offsetTop, behavior: 'smooth'});
            } else {
                const msgs = Array.isArray(json.errors) ? json.errors.join('<br>') : (json.message || 'Erreur');
                feedback.innerHTML = '<div class="alert alert-danger">' + msgs + '</div>';
            }
        })
        .catch(err => {
            feedback.innerHTML = '<div class="alert alert-danger">Erreur lors de la requête.</div>';
            console.error(err);
        });
    });
})();
</script>

<?php include '../includes/footer.php'; ?>
