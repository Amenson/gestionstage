<?php
require_once('../includes/security.php');
require_once('../config/db.php');
require_once('../includes/csrf.php');
require_once('../includes/helpers.php');

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $sexe = trim($_POST['sexe'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $voie = trim($_POST['voie'] ?? null);
    $cp = trim($_POST['cp'] ?? null);
    $ville = trim($_POST['ville'] ?? null);
    $tel = trim($_POST['tel'] ?? null);
    $mail = trim($_POST['mail'] ?? null);
    $rawPassword = $_POST['password'] ?? '';
    $statut = trim($_POST['statut'] ?? null);
    $token = $_POST['csrf_token'] ?? '';

    if ($nom === '' || $prenom === '' || $sexe === '' || $date === '') {
        $errors[] = 'Les champs obligatoires doivent être renseignés.';
    }
    if (!in_array($sexe, ['M', 'F'])) {
        $errors[] = 'Sexe invalide.';
    }
    if (!csrf_validate($token)) {
        $errors[] = 'Token CSRF invalide.';
    }

    if (!empty($mail) && !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email invalide.';
    }

    if (!empty($rawPassword) && strlen($rawPassword) < 6) {
        $errors[] = 'Le mot de passe doit faire au moins 6 caractères.';
    }

    if (empty($errors)) {
        // Vérifier unicité de l'email
        if (!empty($mail)) {
            $chk = $pdo->prepare('SELECT COUNT(*) AS c FROM Etudiant WHERE mailEtud = ?');
            $chk->execute([$mail]);
            $row = $chk->fetch(PDO::FETCH_ASSOC);
            if ($row && $row['c'] > 0) {
                $errors[] = 'Un étudiant avec cet email existe déjà.';
            }
        }
    }

    if (empty($errors)) {
        // Si un mot de passe est fourni, on le hache
        $passwordHash = null;
        if (!empty($rawPassword)) {
            $passwordHash = password_hash($rawPassword, PASSWORD_DEFAULT);
        }

        if ($passwordHash !== null) {
            $stmt = $pdo->prepare("INSERT INTO Etudiant (nomEtud, prenomEtud, sexeEtud, dateNaissEtud, voieEtud, cpEtud, villeEtud, telEtud, mailEtud, password, statutEtud) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$nom, $prenom, $sexe, $date, $voie, $cp, $ville, $tel, $mail, $passwordHash, $statut]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO Etudiant (nomEtud, prenomEtud, sexeEtud, dateNaissEtud, voieEtud, cpEtud, villeEtud, telEtud, mailEtud, statutEtud) VALUES (?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$nom, $prenom, $sexe, $date, $voie, $cp, $ville, $tel, $mail, $statut]);
        }

        if ($isAjax) {
            json_response(['status' => 'ok', 'message' => 'Étudiant ajouté avec succès.']);
        } else {
            $message = 'Étudiant ajouté avec succès.';
        }
    } else {
        if ($isAjax) {
            json_response(['status' => 'error', 'errors' => $errors], 422);
        } else {
            $message = implode(' ', $errors);
        }
    }
}

include '../includes/header.php';
?>

<div class="container-form">
    <div class="card">
        <h2>Ajouter Étudiant</h2>

        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'succ') !== false ? 'alert-success' : 'alert-danger' ?>"><?= esc($message) ?></div>
        <?php endif; ?>

        <form id="etudiantForm" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
            <input class="form-control" type="text" name="nom" placeholder="Nom" required>
            <input class="form-control" type="text" name="prenom" placeholder="Prénom" required>
            <select class="form-control" name="sexe" required>
                <option value="">-- Sexe --</option>
                <option value="M">Masculin</option>
                <option value="F">Féminin</option>
            </select>
            <input class="form-control" type="date" name="date" required>
            <input class="form-control" type="text" name="voie" placeholder="Voie">
            <input class="form-control" type="text" name="cp" placeholder="Code Postal">
            <input class="form-control" type="text" name="ville" placeholder="Ville">
            <input class="form-control" type="tel" name="tel" placeholder="Téléphone">
            <input class="form-control" type="email" name="mail" placeholder="Email">
            <input class="form-control" type="password" name="password" placeholder="Mot de passe (optionnel)">
            <input class="form-control" type="text" name="statut" placeholder="Statut (Ex: Actif)">

            <div class="d-flex justify-content-between mt-4">
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Ajouter Étudiant
                </button>
            </div>
        </form>

        <div id="successMsg" class="alert alert-success d-none" role="alert">
            Étudiant ajouté avec succès !
        </div>
    </div>
</div>

<script>
const form = document.getElementById('etudiantForm');
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
