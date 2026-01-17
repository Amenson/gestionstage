<?php
require_once("../includes/security.php");
require_once("../config/db.php");
require_once("../includes/csrf.php");
require_once("../includes/helpers.php");

$page_title = "Ajouter Entreprise";
$message = "";
$message_type = "";

$types = $pdo->query("SELECT * FROM TypeEntreprise")->fetchAll();
$domaines = $pdo->query("SELECT * FROM DomaineActivite")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $token = $_POST['csrf_token'] ?? '';
        $nom = trim($_POST['nom'] ?? '');
        $numVoie = !empty($_POST['numVoie']) ? (int)$_POST['numVoie'] : null;
        $voie = !empty($_POST['voie']) ? trim($_POST['voie']) : null;
        $cp = !empty($_POST['cp']) ? trim($_POST['cp']) : null;
        $ville = !empty($_POST['ville']) ? trim($_POST['ville']) : null;
        $tel = !empty($_POST['tel']) ? trim($_POST['tel']) : null;
        $mail = !empty($_POST['mail']) ? trim($_POST['mail']) : null;
        $type = (int)($_POST['type'] ?? 0);
        $domaine = (int)($_POST['domaine'] ?? 0);

        if (!csrf_validate($token)) {
            throw new Exception('Token CSRF invalide.');
        }
        if ($nom === '' || $type <= 0 || $domaine <= 0) {
            throw new Exception('Veuillez renseigner tous les champs obligatoires.');
        }

        $stmt = $pdo->prepare("INSERT INTO Entreprise 
        (nomEntreprise, numVoieEntreprise, voieEntreprise, cpEntreprise, villeEntreprise, telEntreprise, mailEntreprise, fkTypeEntreprise, fkDomaineAct)
        VALUES (?,?,?,?,?,?,?,?,?)");

        $stmt->execute([
            $nom,
            $numVoie,
            $voie,
            $cp,
            $ville,
            $tel,
            $mail,
            $type,
            $domaine
        ]);

        header("Location: ajouter_entreprise.php?success=1");
        exit;
    } catch (PDOException $e) {
        $message = "Erreur lors de l'ajout : " . $e->getMessage();
        $message_type = "danger";
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = "warning";
    }
}

if (isset($_GET["success"])) {
    $message = "Entreprise ajoutée avec succès";
    $message_type = "success";
}
?>
<!-- Ajoute ce CSS dans <head> ou dans un fichier style.css -->
<style>
:root {
    --primary-blue: #0d6efd;
    --secondary-orange: #fd7e14;
    --bg-light: #f8f9fa;
    --card-bg: #ffffff;
    --text-dark: #212529;
    --hover-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--bg-light);
    color: var(--text-dark);
}

/* Card Form */
.card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}
.card-header {
    background-color: var(--primary-blue);
    color: #fff;
    font-weight: 600;
    font-size: 1.1rem;
}
.card-body {
    padding: 25px;
}

/* Form inputs */
.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #ced4da;
    transition: all 0.2s;
}
.form-control:focus, .form-select:focus {
    border-color: var(--primary-blue);
    box-shadow: 0 0 5px rgba(13,110,253,0.25);
    outline: none;
}

/* Labels */
.form-label {
    font-weight: 500;
}

/* Buttons */
.btn-primary {
    background-color: var(--primary-blue);
    border-color: var(--primary-blue);
    transition: all 0.3s;
}
.btn-primary:hover {
    background-color: var(--secondary-orange);
    border-color: var(--secondary-orange);
}
.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}
.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

/* Alerts */
.alert {
    border-radius: 6px;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 576px) {
    .card-body {
        padding: 15px;
    }
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <main class="flex-grow-1 p-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 col-lg-6 mx-auto">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h2 class="h4 mb-0"><i class="bi bi-building"></i> Ajouter une Entreprise</h2>
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                                <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($message) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom de l'entreprise <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom de l'entreprise" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="numVoie" class="form-label">Numéro de voie</label>
                                        <input type="number" class="form-control" id="numVoie" name="numVoie" placeholder="Numéro">
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label for="voie" class="form-label">Voie</label>
                                        <input type="text" class="form-control" id="voie" name="voie" placeholder="Rue, Avenue, Boulevard...">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="cp" class="form-label">Code postal</label>
                                        <input type="text" class="form-control" id="cp" name="cp" placeholder="Code postal">
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label for="ville" class="form-label">Ville</label>
                                        <input type="text" class="form-control" id="ville" name="ville" placeholder="Ville">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="tel" class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control" id="tel" name="tel" placeholder="Téléphone">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="mail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="mail" name="mail" placeholder="email@exemple.com">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="type" class="form-label">Type d'entreprise <span class="text-danger">*</span></label>
                                        <select class="form-select" id="type" name="type" required>
                                            <option value="">Sélectionner un type</option>
                                            <?php foreach($types as $t): ?>
                                                <option value="<?= htmlspecialchars($t["codeTypeEntr"]) ?>">
                                                    <?= htmlspecialchars($t["libEntr"]) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="domaine" class="form-label">Domaine d'activité <span class="text-danger">*</span></label>
                                        <select class="form-select" id="domaine" name="domaine" required>
                                            <option value="">Sélectionner un domaine</option>
                                            <?php foreach($domaines as $d): ?>
                                                <option value="<?= htmlspecialchars($d["codeDomaineAct"]) ?>">
                                                    <?= htmlspecialchars($d["libDomaineAct"]) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <a href="dashboard.php" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Retour
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Ajouter l'entreprise
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    const alertDiv = document.querySelector(".alert");

    if (form) {
        form.addEventListener("submit", function(e) {
            // Optionnel: on peut faire un fetch pour AJAX, sinon laisser le POST classique
            if(alertDiv) {
                // Faire disparaître l'alerte après 3 secondes
                setTimeout(() => {
                    alertDiv.classList.remove("show");
                }, 3000);
            }
        });
    }
});
</script>
