<?php
session_start();
require_once("../config/db.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["login"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM Professeur WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if ($user && $password == $user["password"]) {
        $_SESSION["admin"] = $user["login"];
        $_SESSION["full_name"] = $user["login"];
        header("Location: ../index.php?login=success&role=admin");
        exit;
    } else {
        $message = "Login ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion Administrateur</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
:root {
    --primary-blue: #0d6efd;
    --secondary-orange: #fd7e14;
    --bg-light: #f8f9fa;
}

body {
    background-color: var(--bg-light);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
}

.card-login {
    background-color: #fff;
    border-radius: 12px;
    padding: 30px 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
    transition: all 0.3s ease;
}

.card-login h2 {
    text-align: center;
    color: var(--primary-blue);
    margin-bottom: 25px;
    font-weight: 700;
}

.form-control {
    border-radius: 6px;
    margin-bottom: 15px;
    padding: 10px 12px;
}

.form-control:focus {
    border-color: var(--primary-blue);
    box-shadow: 0 0 5px rgba(13,110,253,0.25);
    outline: none;
}

button {
    width: 100%;
    padding: 12px;
    border-radius: 6px;
    background-color: var(--primary-blue);
    color: #fff;
    font-weight: 600;
    border: none;
    transition: all 0.3s ease;
}

button:hover {
    background-color: var(--secondary-orange);
}

.alert {
    text-align: center;
    font-weight: 500;
}
</style>
</head>
<body>

<div class="card-login">
    <h2>Connexion Administrateur</h2>
    <?php if($message): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="POST">
        <input class="form-control" type="text" name="login" placeholder="Login" required>
        <input class="form-control" type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit"><i class="bi bi-box-arrow-in-right"></i> Se connecter</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
