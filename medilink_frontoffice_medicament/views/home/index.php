<?php require __DIR__ . '/../layouts/header.php'; ?>

<section class="hero-section">
    <div class="hero-content">
        <h1>Prenez rendez-vous facilement avec des professionnels de santé</h1>
        <p>
            MediLink vous permet de consulter des informations utiles et d'accéder à un espace clair
            pour parcourir les médicaments disponibles.
        </p>
        <form class="hero-search" method="GET" action="index.php">
            <input type="hidden" name="action" value="medicaments">
            <input type="text" name="search" placeholder="Rechercher un médicament, une forme, un fabricant...">
            <button type="submit">Rechercher</button>
        </form>
    </div>
</section>

<section class="service-strip" id="services">
    <div class="service-item">Trouver un spécialiste</div>
    <div class="service-item">Réserver en ligne</div>
    <div class="service-item">Suivi personnalisé</div>
</section>

<section class="section-block">
    <div class="section-title-row">
        <h2>Médicaments mis en avant</h2>
        <a href="index.php?action=medicaments">Voir tout</a>
    </div>

    <div class="card-grid">
        <?php foreach ($featuredMedicaments as $medicament): ?>
            <article class="medicine-card">
                <div class="medicine-icon">💊</div>
                <h3><?= htmlspecialchars($medicament['nom']) ?></h3>
                <p class="meta"><?= htmlspecialchars($medicament['dosage']) ?> - <?= htmlspecialchars($medicament['forme']) ?></p>
                <p><?= htmlspecialchars(mb_strimwidth($medicament['description'], 0, 95, '...')) ?></p>
                <a href="index.php?action=show_medicament&id=<?= (int) $medicament['id'] ?>">Voir les détails</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
