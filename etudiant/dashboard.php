<?php
require_once("../includes/security_etudiant.php");
require_once("../config/db.php");

// ID étudiant depuis la session
$idEtud = $_SESSION["idEtudiant"];

$page_title = "Tableau de bord";

// Récupérer les informations de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM Etudiant WHERE codeEtud = ?");
$stmt->execute([$idEtud]);
$etudiant = $stmt->fetch();

// Statistiques générales
// Nombre total de stages disponibles
$totalStages = $pdo->query("SELECT COUNT(*) as total FROM Stage WHERE fkEtudiant IS NULL")->fetch()["total"];

// Stage affecté à l'étudiant
$stmt = $pdo->prepare("
    SELECT s.*, e.nomEntreprise, e.villeEntreprise, ts.libTypeStage, ts.dureeStage
    FROM Stage s
    LEFT JOIN Entreprise e ON s.fkEntreprise = e.numSiret
    LEFT JOIN TypeStage ts ON s.fkTypeStage = ts.codeTypeStage
    WHERE s.fkEtudiant = ?
");
$stmt->execute([$idEtud]);
$monStage = $stmt->fetch();

// Notes du stage (si stage affecté)
$moyenne = null;
$notes = [];
if ($monStage) {
    $stmt = $pdo->prepare("SELECT * FROM NoteStage WHERE numOffre = ?");
    $stmt->execute([$monStage["numOffre"]]);
    $notes = $stmt->fetchAll();
    
    if (count($notes) > 0) {
        $somme = 0;
        foreach ($notes as $note) {
            $somme += $note["noteStage"];
        }
        $moyenne = round($somme / count($notes), 2);
    }
}

// Stages récents disponibles
$stagesRecents = $pdo->query("
    SELECT s.*, e.nomEntreprise, ts.libTypeStage
    FROM Stage s
    LEFT JOIN Entreprise e ON s.fkEntreprise = e.numSiret
    LEFT JOIN TypeStage ts ON s.fkTypeStage = ts.codeTypeStage
    WHERE s.fkEtudiant IS NULL
    ORDER BY s.dateParution DESC
    LIMIT 5
")->fetchAll();
?>

<?php include '../includes/header_etudiant.php'; ?>

<div class="d-flex">
    <main class="flex-grow-1 p-4">
        <div class="container-fluid">
            <!-- En-tête avec salutation -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="welcome-card">
                        <div class="d-flex align-items-center">
                            <div class="welcome-icon">
                                <i class="bi bi-person-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h1 class="h3 mb-1">Bienvenue, <?= htmlspecialchars($etudiant["prenomEtud"] ?? "Étudiant") ?> !</h1>
                                <p class="text-muted mb-0"><?= htmlspecialchars($etudiant["nomEtud"] ?? "") ?> - <?= htmlspecialchars($etudiant["mailEtud"] ?? "") ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="stat-card stat-primary">
                        <div class="stat-icon">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?= $totalStages ?></h3>
                            <p class="stat-label">Stages disponibles</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card stat-success">
                        <div class="stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?= $monStage ? "1" : "0" ?></h3>
                            <p class="stat-label">Stage affecté</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card stat-warning">
                        <div class="stat-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?= count($notes) ?></h3>
                            <p class="stat-label">Notes enregistrées</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card stat-info">
                        <div class="stat-icon">
                            <i class="bi bi-star"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number"><?= $moyenne ?? "N/A" ?></h3>
                            <p class="stat-label">Moyenne / 20</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Mon Stage -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h5 mb-0"><i class="bi bi-briefcase-fill"></i> Mon Stage</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($monStage): ?>
                                <div class="stage-info">
                                    <h4 class="mb-3"><?= htmlspecialchars($monStage["libStage"]) ?></h4>
                                    
                                    <div class="info-item">
                                        <i class="bi bi-building text-primary"></i>
                                        <strong>Entreprise :</strong>
                                        <span><?= htmlspecialchars($monStage["nomEntreprise"]) ?></span>
                                    </div>
                                    
                                    <div class="info-item">
                                        <i class="bi bi-geo-alt text-primary"></i>
                                        <strong>Ville :</strong>
                                        <span><?= htmlspecialchars($monStage["villeEntreprise"] ?? "Non spécifiée") ?></span>
                                    </div>
                                    
                                    <div class="info-item">
                                        <i class="bi bi-calendar text-primary"></i>
                                        <strong>Période :</strong>
                                        <span><?= htmlspecialchars($monStage["periodeStage"] ?? "Non spécifiée") ?></span>
                                    </div>
                                    
                                    <div class="info-item">
                                        <i class="bi bi-clock text-primary"></i>
                                        <strong>Durée :</strong>
                                        <span><?= htmlspecialchars($monStage["dureeStage"] ?? "Non spécifiée") ?></span>
                                    </div>
                                    
                                    <?php if ($monStage["remunerationStage"]): ?>
                                    <div class="info-item">
                                        <i class="bi bi-currency-euro text-primary"></i>
                                        <strong>Rémunération :</strong>
                                        <span><?= htmlspecialchars($monStage["remunerationStage"]) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-3">
                                        <a href="mon_stage.php" class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye"></i> Voir les détails
                                        </a>
                                        <a href="ma_note.php" class="btn btn-success btn-sm">
                                            <i class="bi bi-file-earmark-text"></i> Mes notes
                                        </a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-inbox display-1 text-muted"></i>
                                    <p class="text-muted mt-3">Aucun stage affecté pour le moment</p>
                                    <a href="consulter_stages.php" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Consulter les offres
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Mes Notes -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h3 class="h5 mb-0"><i class="bi bi-file-earmark-text-fill"></i> Mes Notes</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($monStage && count($notes) > 0): ?>
                                <div class="notes-summary">
                                    <div class="moyenne-display">
                                        <div class="moyenne-circle">
                                            <span class="moyenne-value"><?= $moyenne ?></span>
                                            <span class="moyenne-max">/ 20</span>
                                        </div>
                                        <p class="text-center mt-2 mb-0"><strong>Moyenne générale</strong></p>
                                    </div>
                                    
                                    <div class="notes-list mt-4">
                                        <h5 class="mb-3">Détail des notes :</h5>
                                        <?php foreach ($notes as $note): ?>
                                            <div class="note-item">
                                                <span class="note-critere"><?= htmlspecialchars($note["numCritere"]) ?></span>
                                                <span class="note-value"><?= htmlspecialchars($note["noteStage"]) ?>/20</span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <a href="ma_note.php" class="btn btn-success btn-sm w-100">
                                            <i class="bi bi-arrow-right"></i> Voir toutes mes notes
                                        </a>
                                    </div>
                                </div>
                            <?php elseif ($monStage): ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-hourglass-split display-1 text-muted"></i>
                                    <p class="text-muted mt-3">Aucune note enregistrée pour le moment</p>
                                    <small class="text-muted">Les notes seront disponibles après l'évaluation de votre stage</small>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-info-circle display-1 text-muted"></i>
                                    <p class="text-muted mt-3">Aucun stage affecté</p>
                                    <p class="text-muted small">Les notes seront disponibles une fois qu'un stage vous sera affecté</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stages récents disponibles -->
            <?php if (count($stagesRecents) > 0): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <h3 class="h5 mb-0"><i class="bi bi-clock-history"></i> Stages récents disponibles</h3>
                            <a href="consulter_stages.php" class="btn btn-light btn-sm">
                                Voir tout <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Stage</th>
                                            <th>Entreprise</th>
                                            <th>Type</th>
                                            <th>Date de parution</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stagesRecents as $stage): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($stage["libStage"]) ?></strong></td>
                                            <td><?= htmlspecialchars($stage["nomEntreprise"]) ?></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($stage["libTypeStage"]) ?></span></td>
                                            <td><?= date("d/m/Y", strtotime($stage["dateParution"])) ?></td>
                                            <td>
                                                <a href="consulter_stages.php?id=<?= $stage["numOffre"] ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Voir
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Actions rapides -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h3 class="h5 mb-0"><i class="bi bi-lightning-fill"></i> Actions rapides</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="consulter_stages.php" class="action-card">
                                        <i class="bi bi-search"></i>
                                        <span>Consulter les stages</span>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="mon_stage.php" class="action-card">
                                        <i class="bi bi-briefcase"></i>
                                        <span>Mon stage</span>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="ma_note.php" class="action-card">
                                        <i class="bi bi-file-earmark-text"></i>
                                        <span>Mes notes</span>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="../auth/logout.php" class="action-card action-danger">
                                        <i class="bi bi-box-arrow-right"></i>
                                        <span>Déconnexion</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- CSS personnalisé -->
<link rel="stylesheet" href="../assets/css/dashboard_etudiant.css">

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/dashboard_etudiant.js"></script>

<?php include '../includes/footer.php'; ?>

