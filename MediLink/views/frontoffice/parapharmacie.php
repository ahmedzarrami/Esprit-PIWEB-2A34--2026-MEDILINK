<!-- ===== PAGE: PARAPHARMACIE ===== -->
<div id="page-parapharmacie" class="page active">
  <div class="hero" style="min-height: 300px; padding: 60px 0;">
    <div class="hero-badge"><span></span> Santé & Bien-être</div>
    <h1 class="hero-title">Votre Parapharmacie<br><em>en ligne</em></h1>
    <p class="hero-sub">Retrouvez tous vos produits de soins, hygiène et compléments alimentaires sélectionnés par nos experts.</p>
  </div>

  <div class="section">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; flex-wrap: wrap; gap: 20px;">
      <div>
        <div class="section-title" style="text-align: left; margin: 0;">Notre Catalogue</div>
        <div class="section-sub" style="text-align: left; margin: 0;">Filtrez par catégorie pour trouver ce dont vous avez besoin.</div>
      </div>
      <div class="filter-tabs" style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 10px; max-width: 100%;">
        <a href="index.php?module=parapharmacie" class="filter-tab <?= !isset($_GET['categorie']) ? 'active' : '' ?>">Tous</a>
        <?php foreach ($categories as $cat): ?>
          <a href="index.php?module=parapharmacie&categorie=<?= urlencode($cat) ?>" class="filter-tab <?= (isset($_GET['categorie']) && $_GET['categorie'] === $cat) ? 'active' : '' ?>">
            <?= htmlspecialchars($cat) ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
      <?php if (empty($produits)): ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 60px; background: rgba(255,255,255,0.5); border-radius: 24px; border: 1px dashed rgba(0,0,0,0.1);">
          <div style="font-size: 48px; margin-bottom: 20px;">📦</div>
          <h3 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 10px;">Aucun produit trouvé</h3>
          <p style="color: #64748b;">Désolé, il n'y a aucun produit disponible dans cette catégorie pour le moment.</p>
          <a href="index.php?module=parapharmacie" class="btn btn-outline" style="margin-top: 20px;">Voir tout le catalogue</a>
        </div>
      <?php else: ?>
        <?php foreach ($produits as $p): ?>
          <div class="feature-card product-card" style="display: flex; flex-direction: column; height: 100%; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
            <div class="product-badge" style="position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.9); padding: 5px 12px; border-radius: 100px; font-size: 12px; font-weight: 700; color: #2563eb; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
              <?= htmlspecialchars($p['categorie']) ?>
            </div>
            <div class="feature-icon" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); margin: -24px -24px 20px -24px; height: 200px; border-radius: 20px 20px 0 0; display: flex; align-items: center; justify-content: center; font-size: 64px; overflow: hidden; position: relative;">
              <?php 
                $icons = [
                  'Soins visage' => '🧴',
                  'Soins corps' => '🫧',
                  'Hygiène' => '🪥',
                  'Compléments alimentaires' => '💊',
                  'Bébé & Maman' => '🍼',
                  'Capillaire' => '💆',
                  'Solaire' => '☀️',
                  'Minceur' => '⚖️',
                  'Orthopédie' => '🦴',
                  'Autre' => '📦'
                ];
                echo $icons[$p['categorie']] ?? '📦';
              ?>
              <div class="card-glow" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, rgba(37,99,235,0.05) 0%, transparent 70%);"></div>
            </div>
            <div class="feature-title" style="font-size: 18px; margin-bottom: 8px;"><?= htmlspecialchars($p['nom']) ?></div>
            <div class="feature-desc" style="flex-grow: 1; margin-bottom: 20px;"><?= htmlspecialchars($p['description']) ?></div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05);">
              <div class="product-price" style="font-size: 20px; font-weight: 800; color: #0f172a;">
                <?= number_format((float)$p['prix'], 3, ',', ' ') ?> <small style="font-size: 12px; font-weight: 600; color: #64748b;">DT</small>
              </div>
              <button class="btn btn-primary" style="padding: 10px 16px; font-size: 14px;">
                Commander
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<style>
  .filter-tab {
    padding: 10px 20px;
    border-radius: 100px;
    background: rgba(255,255,255,0.5);
    border: 1px solid rgba(0,0,0,0.05);
    color: #64748b;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    white-space: nowrap;
  }
  .filter-tab:hover {
    background: #fff;
    color: #2563eb;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }
  .filter-tab.active {
    background: #2563eb;
    color: #fff;
    border-color: #2563eb;
    box-shadow: 0 8px 20px rgba(37,99,235,0.2);
  }
  .product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.08);
  }
  .product-card:hover .feature-icon {
    transform: scale(1.02);
  }
</style>
