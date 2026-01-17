<?php
require_once('../includes/security.php');
require_once('../config/db.php');
require_once('../includes/csrf.php');
require_once('../includes/helpers.php');

$types = $pdo->query("SELECT * FROM TypeStage ORDER BY libTypeStage")->fetchAll();
$entreprises = $pdo->query("SELECT * FROM Entreprise ORDER BY nomEntreprise")->fetchAll();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    $lib = trim($_POST['lib'] ?? '');
    $periode = trim($_POST['periode'] ?? null);
    $mois = !empty($_POST['mois']) ? (int)$_POST['mois'] : null;
    $desc = trim($_POST['desc'] ?? null);
    $fonctions = trim($_POST['fonctions'] ?? null);
    $remu = trim($_POST['remuneration'] ?? null);
    $mail = trim($_POST['mail'] ?? null);
    $type = (int)($_POST['type'] ?? 0);
    $entreprise = (int)($_POST['entreprise'] ?? 0);
    $token = $_POST['csrf_token'] ?? '';

    $errors = [];
    if ($lib === '') $errors[] = 'Titre du stage requis.';
    if ($type <= 0) $errors[] = 'Type de stage invalide.';
    if ($entreprise <= 0) $errors[] = 'Entreprise invalide.';
    if (!csrf_validate($token)) $errors[] = 'Token CSRF invalide.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO Stage (libStage, dateParution, periodeStage, moisStage, descStage, fonctionsStage, remunerationStage, mailContact, fkTypeStage, fkEntreprise) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$lib, date('Y-m-d'), $periode, $mois, $desc, $fonctions, $remu, $mail, $type, $entreprise]);

        if ($isAjax) json_response(['status' => 'ok', 'message' => 'Stage ajouté.']);
        $message = 'Stage ajouté avec succès.';
    } else {
        if ($isAjax) json_response(['status' => 'error', 'errors' => $errors], 422);
        $message = implode(' ', $errors);
    }
}

include '../includes/header.php';
?>

<div class="container-form">
    <div class="card">
        <h2>Ajouter Stage</h2>

        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'succ') !== false ? 'alert-success' : 'alert-danger' ?>"><?= esc($message) ?></div>
        <?php endif; ?>

        <form id="stageForm" method="POST">
            <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
            <input type="text" name="lib" class="form-control" placeholder="Titre du stage" required>
            <input type="text" name="periode" class="form-control" placeholder="Période">
            <input type="number" name="mois" class="form-control" placeholder="Nombre de mois">
            <textarea name="desc" class="form-control" placeholder="Description"></textarea>
            <textarea name="fonctions" class="form-control" placeholder="Fonctions"></textarea>
            <input type="text" name="remuneration" class="form-control" placeholder="Rémunération">
            <input type="email" name="mail" class="form-control" placeholder="Mail contact">

            <select name="type" class="form-select" required>
                <option value="">-- Sélectionner type --</option>
                <?php foreach($types as $t): ?>
                    <option value="<?= esc($t['codeTypeStage']) ?>"><?= esc($t['libTypeStage']) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="entreprise" class="form-select mt-2" required>
                <option value="">-- Sélectionner entreprise --</option>
                <?php foreach($entreprises as $e): ?>
                    <option value="<?= esc($e['numSiret']) ?>"><?= esc($e['nomEntreprise']) ?></option>
                <?php endforeach; ?>
            </select>

            <div class="d-flex justify-content-between mt-3">
                <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Ajouter Stage</button>
            </div>
        </form>

        <div id="successMsg" class="alert alert-success d-none">
            Stage ajouté avec succès !
        </div>
    </div>
</div>

<script>
const form = document.getElementById('stageForm');
const successMsg = document.getElementById('successMsg');

form.addEventListener('submit', function(event) {
    event.preventDefault();

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
                window.appFetch.notify((data.errors || [data.message || 'Erreur']).join('\n'), 'danger');
            }
        })
        .catch(err => {
            const msg = (err && err.data && err.data.errors) ? err.data.errors.join(' / ') : (err && err.error) ? err.error.message : 'Erreur lors de l\'ajout';
            window.appFetch.notify(msg, 'danger');
        });
});
</script>

<?php include '../includes/footer.php'; ?>
