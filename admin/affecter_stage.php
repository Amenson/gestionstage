<?php
require_once('../includes/security.php');
require_once('../config/db.php');
require_once('../includes/csrf.php');
require_once('../includes/helpers.php');

$message = '';

$stages = $pdo->query("SELECT * FROM Stage WHERE fkEtudiant IS NULL ORDER BY libStage")->fetchAll();
$etudiants = $pdo->query("SELECT * FROM Etudiant ORDER BY nomEtud")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    $stage = (int)($_POST['stage'] ?? 0);
    $etud = (int)($_POST['etudiant'] ?? 0);
    $token = $_POST['csrf_token'] ?? '';

    if (!csrf_validate($token)) {
        $message = 'Token CSRF invalide.';
    } elseif ($stage <= 0 || $etud <= 0) {
        $message = 'Données invalides.';
    } else {
        $stmt = $pdo->prepare('UPDATE Stage SET fkEtudiant = ? WHERE numOffre = ?');
        $stmt->execute([$etud, $stage]);
        $message = 'Stage affecté avec succès.';
    }

    if ($isAjax) json_response(['status' => empty($message) ? 'error' : 'ok', 'message' => $message]);
}

include '../includes/header.php';
?>

<div class="container-form">
    <div class="card">
        <h2>Affecter Stage</h2>

        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'succ') !== false ? 'alert-success' : 'alert-danger' ?>"><?= esc($message) ?></div>
        <?php endif; ?>

        <form id="affectStageForm" method="POST">
            <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">

            <label for="stage" class="form-label">Sélectionner un Stage</label>
            <select id="stage" name="stage" class="form-select" required>
                <option value="">-- Choisir un stage --</option>
                <?php foreach($stages as $s): ?>
                    <option value="<?= esc($s['numOffre']) ?>"><?= esc($s['libStage']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="etudiant" class="form-label">Sélectionner un Étudiant</label>
            <select id="etudiant" name="etudiant" class="form-select" required>
                <option value="">-- Choisir un étudiant --</option>
                <?php foreach($etudiants as $e): ?>
                    <option value="<?= esc($e['codeEtud']) ?>"><?= esc($e['nomEtud']) ?></option>
                <?php endforeach; ?>
            </select>

            <div class="d-flex justify-content-between mt-3">
                <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
                <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle"></i> Affecter</button>
            </div>
        </form>

        <div id="successMsg" class="alert alert-success d-none" role="alert">
            Stage affecté avec succès !
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Gestion du message succès sans rechargement
    const form = document.getElementById('affectStageForm');
    const successMsg = document.getElementById('successMsg');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        // Simple fetch POST pour envoyer le formulaire
        const formData = new FormData(form);
        window.appFetch.fetchJson(window.location.href, { method: 'POST', body: formData })
            .then(res => {
                if (!res.ok) throw res;
                const data = res.data || {};
                if (data.status === 'ok') {
                    successMsg.classList.remove('d-none');
                    successMsg.scrollIntoView({behavior: 'smooth'});
                    form.reset();
                } else {
                    window.appFetch.notify(data.message || 'Erreur', 'danger');
                }
            })
            .catch(err => {
                const msg = (err && err.data && err.data.errors) ? err.data.errors.join(' / ') : (err && err.error) ? err.error.message : 'Erreur lors de l\'affectation';
                window.appFetch.notify(msg, 'danger');
            });
    });
</script>

<?php include '../includes/footer.php'; ?>
