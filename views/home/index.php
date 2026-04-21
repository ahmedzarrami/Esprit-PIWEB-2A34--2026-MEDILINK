<?php require __DIR__ . '/../layouts/header.php'; ?>

<!-- HERO -->
<div class="hero">
    <div class="hero-inner">
        <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            Service disponible du lundi au samedi · 8h00 – 18h00
        </div>
        <h1>Consultez et gérez vos<br><em>médicaments & ordonnances</em></h1>
        <p>Recherchez un médicament, consultez les détails, et créez vos ordonnances en quelques secondes.</p>

        <form class="hero-search-form" method="GET" action="index.php">
            <input type="hidden" name="action" value="medicaments">
            <input type="text" name="search" placeholder="Rechercher un médicament, une forme, un fabricant…">
            <button type="submit">Rechercher</button>
        </form>
    </div>
</div>

<!-- STATS -->
<div class="stats-strip">
    <div class="stats-inner">
        <div class="stat-item">
            <div class="stat-num">Médicaments</div>
            <div class="stat-label">Catalogue complet</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">Ordonnances</div>
            <div class="stat-label">Création rapide</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">&lt; 2 min</div>
            <div class="stat-label">Temps moyen</div>
        </div>
    </div>
</div>

<!-- FEATURED MEDICAMENTS -->
<div class="main-content">
    <div class="section-heading">Médicaments mis en avant</div>

    <div class="rech-grid">
        <?php foreach ($featuredMedicaments as $med): ?>
        <article class="rech-doc-card">
            <div class="rech-avatar" style="background:linear-gradient(135deg,#1a56db,#6694f8);color:#fff;font-size:22px;">💊</div>
            <div class="rech-doc-name"><?= htmlspecialchars($med['nom']) ?></div>
            <div class="rech-doc-spec"><?= htmlspecialchars((string)($med['forme'] ?? '')) ?></div>
            <?php if (!empty($med['fabricant'])): ?>
            <div class="rech-doc-city">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <?= htmlspecialchars($med['fabricant']) ?>
            </div>
            <?php endif; ?>
            <div class="rech-doc-meta">
                <div class="rech-meta-item">
                    <strong><?= number_format((float)$med['prix'], 2) ?> €</strong>Prix
                </div>
                <div class="rech-meta-item">
                    <strong><?= (int)$med['stock'] ?></strong>Stock
                </div>
            </div>
            <a href="index.php?action=show_medicament&id=<?= (int)$med['id'] ?>" class="btn-rech-rdv">Voir les détails</a>
        </article>
        <?php endforeach; ?>

        <?php if (empty($featuredMedicaments)): ?>
        <div class="rech-empty">
            <div class="rech-empty-icon">💊</div>
            <strong>Aucun médicament disponible</strong>
            <span>La base de données est vide.</span>
        </div>
        <?php endif; ?>
    </div>

    <div style="text-align:center;margin-top:28px;">
        <a href="index.php?action=medicaments" class="btn-admin" style="display:inline-flex;">Voir tous les médicaments</a>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
