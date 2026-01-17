<?php
require_once("../config/db.php");
require_once(__DIR__ . '/../includes/csrf.php');
require_once(__DIR__ . '/../includes/helpers.php');

// Charger les pays
$pays = $pdo->query("SELECT * FROM Pays")->fetchAll();

$msg = "";

if(isset($_POST["btn"])){
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $token = $_POST['csrf_token'] ?? '';
        if (!csrf_validate($token)) {
            $msg = 'Token CSRF invalide.';
        } else {
            $nom = trim($_POST["nom"] ?? '');
            $prenom = trim($_POST["prenom"] ?? '');
            $mail = trim($_POST["mail"] ?? '');
            $rawPassword = $_POST["password"] ?? '';
            $password = password_hash($rawPassword, PASSWORD_DEFAULT);
        }
    }
    if (empty($errors)) {
        // Vérifier si email existe déjà
        $check = $pdo->prepare("SELECT * FROM Etudiant WHERE mailEtud = ?");
        $check->execute([$mail]);
        if ($check->rowCount() > 0) {
            $errors[] = "❌ Cet email existe déjà";
        }
        if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO Etudiant
            (nomEtud, prenomEtud, mailEtud, password, sexeEtud, dateNaissEtud, voieEtud, cpEtud, villeEtud, telEtud, statutEtud, fkPays)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->execute([
            $nom,
            $prenom,
            $mail,
            $password,
            $sexe,
            $date,
            $voie,
            $cp,
            $ville,
            $tel,
            $statut,
            $paysId
        ]);
            if ($stmt->rowCount() > 0) {
                $msg = "✅ Inscription réussie ! Tu peux te connecter.";
            }
            else {
                $errors[] = "❌ Erreur lors de l'inscription";
            }
            
        }
        }

    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription Étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Theme & auth styles -->
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/auth.css">

    <!-- Small app helpers (fetch wrapper, csrf helper, notify) -->
    <script defer src="../assets/js/app-helpers.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-6">

<div class="card shadow">
<div class="card-header text-center">
<h4>🎓 Inscription Étudiant</h4>
</div>

<div class="card-body">

<?php if($msg): ?>
<div class="alert alert-info"><?= esc($msg) ?></div>
<?php endif; ?>

<form method="POST">
<input type="hidden" name="csrf_token" value="<?= esc(csrf_token()) ?>">

<input class="form-control mb-2" name="nom" placeholder="Nom" required>
<input class="form-control mb-2" name="prenom" placeholder="Prénom" required>

<select class="form-select mb-2" name="sexe" required>
<option value="">-- Sexe --</option>
<option value="M">Masculin</option>
<option value="F">Féminin</option>
</select>

<input type="date" class="form-control mb-2" name="date" required>
<input class="form-control mb-2" name="voie" placeholder="Adresse">
<input class="form-control mb-2" name="cp" placeholder="Code postal">
<input class="form-control mb-2" name="ville" placeholder="Ville">
<input class="form-control mb-2" name="tel" placeholder="Téléphone">
<input class="form-control mb-2" name="statut" placeholder="Statut (L3, M1...)">

<input type="email" class="form-control mb-2" name="mail" placeholder="Email" required>
<input type="password" class="form-control mb-3" name="password" placeholder="Mot de passe" required>

<select class="form-select mb-3" name="pays" required>
<option value="">-- Pays --</option>
<?php foreach($pays as $p): ?>
<option value="<?= esc($p["codePays"]) ?>">
<?= esc($p["libPays"]) ?>
</option>
<?php endforeach; ?>
</select>

<button name="btn" class="btn btn-primary w-100">S'inscrire</button>

</form>

</div>
</div>

</div>
</div>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="../assets/js/inscription.js"></script>
</body>
</html>
