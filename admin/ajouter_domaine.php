<?php
require_once("../includes/security.php");
require_once("../config/db.php");
require_once("../includes/csrf.php");
require_once("../includes/helpers.php");

$page_title = "Ajouter Domaine d'Activité";
$message = "";
$message_type = "";

// Récupérer tous les domaines existants
$domaines = $pdo->query("SELECT * FROM DomaineActivite ORDER BY libDomaineAct")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $libelle = trim($_POST["libelle"]);
        $token = $_POST['csrf_token'] ?? '';

        if (!csrf_validate($token)) {
            $message = 'Token CSRF invalide.';
            $message_type = 'danger';
        } else {
            // Vérifier si le domaine existe déjà
            $check = $pdo->prepare("SELECT * FROM DomaineActivite WHERE libDomaineAct = ?");
            $check->execute([$libelle]);

            if ($check->fetch()) {
                $message = "Ce domaine d'activité existe déjà";
                $message_type = "warning";
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    json_response(['status' => 'error', 'errors' => ['Ce domaine existe déjà']], 409);
                }
            } else {
                $stmt = $pdo->prepare("INSERT INTO DomaineActivite(libDomaineAct) VALUES (?)");
                $stmt->execute([$libelle]);
                $lastId = $pdo->lastInsertId();
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    json_response(['status' => 'ok', 'id' => $lastId, 'name' => $libelle]);
                }
                header("Location: ajouter_domaine.php?success=1");
                exit;
            }
        }
    } catch (PDOException $e) {
        $message = "Erreur lors de l'ajout : " . $e->getMessage();
        $message_type = "danger";
    }
}

if (isset($_GET["success"])) {
    $message = "Domaine d'activité ajouté avec succès";
    $message_type = "success";
}
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <main class="flex-grow-1 p-4">
        <div class="container-fluid">
            <div class="row">
                <!-- Formulaire d'ajout -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h2 class="h4 mb-0"><i class="bi bi-plus-circle"></i> Ajouter un Domaine d'Activité</h2>
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                                <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert" id="alertMessage">
                                    <?= htmlspecialchars($message) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form method="POST" id="formDomaine">
                                <input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">
                                <div class="mb-3">
                                    <label for="libelle" class="form-label">Libellé du domaine <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="libelle" 
                                           name="libelle" 
                                           placeholder="Ex: Informatique, Commerce, Santé..." 
                                           required
                                           autocomplete="off">
                                    <div class="invalid-feedback" id="libelleError"></div>
                                    <small class="form-text text-muted">Entrez le nom du domaine d'activité</small>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <a href="dashboard.php" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Retour
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                                        <i class="bi bi-check-circle"></i> Ajouter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Liste des domaines existants -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h3 class="h5 mb-0"><i class="bi bi-list-ul"></i> Domaines existants (<?= count($domaines) ?>)</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($domaines)): ?>
                                <p class="text-muted text-center py-3">Aucun domaine d'activité enregistré</p>
                            <?php else: ?>
                                <div class="list-group" id="listeDomaines">
                                    <?php foreach($domaines as $domaine): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="bi bi-tag-fill text-primary"></i>
                                                <?= htmlspecialchars($domaine["libDomaineAct"]) ?>
                                            </span>
                                            <span class="badge bg-secondary rounded-pill">#<?= $domaine["codeDomaineAct"] ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- CSS personnalisé -->
<link rel="stylesheet" href="../assets/css/domaine.css">

<script src="../assets/js/domaine.js"></script>
<?php include '../includes/footer.php'; ?>
