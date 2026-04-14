<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Dashboard MediLink</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    margin:0;
    background:#f4f7fb;
    font-family:Arial;
}

.sidebar {
    width:240px;
    height:100vh;
    position:fixed;
    background:#2F80ED;
    color:white;
    padding:20px;
}

.sidebar h3 {
    margin-bottom:30px;
}

.sidebar a {
    display:block;
    color:white;
    text-decoration:none;
    margin:15px 0;
}

.main {
    margin-left:240px;
    padding:30px;
}

.card {
    border-radius:10px;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3>MediLink</h3>
    <a href="#">📊 Dashboard</a>
    <a href="#">📅 Rendez-vous</a>
    <a href="#">👨‍⚕️ Médecins</a>
    <a href="#">👥 Patients</a>
</div>

<!-- MAIN -->
<div class="main">

<h2>Tableau de bord</h2>

<div class="row">

    <div class="col-md-4">
        <div class="card shadow p-3">
            <h5>Total RDV</h5>
            <h3><?= count($rendezvous) ?></h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow p-3">
            <h5>Aujourd’hui</h5>
            <h3>5</h3>
        </div>
    </div>

</div>

<div class="card shadow p-4 mt-4">
    <h4>Gestion des rendez-vous</h4>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach($rendezvous as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['date']) ?></td>
                <td><?= htmlspecialchars($r['heure']) ?></td>
                <td>
                    <a class="btn btn-warning btn-sm" href="index.php?action=edit&id=<?= $r['id'] ?>">Modifier</a>
                    <a class="btn btn-danger btn-sm" href="index.php?action=delete&id=<?= $r['id'] ?>">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>
</div>

</div>

</body>
</html>