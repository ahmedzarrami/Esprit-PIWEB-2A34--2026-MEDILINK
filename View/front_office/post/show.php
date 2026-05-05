<?php
$pageTitle = 'Post — Discussion';
require __DIR__ . '/../../layout/front_header.php';
?>

<!-- Breadcrumb -->
<nav>
    <ul class="breadcrumb">
        <li><a href="index.php?controller=forum&action=list"><i class="fas fa-home"></i> Forums</a></li>
        <li class="separator"><i class="fas fa-chevron-right"></i></li>
        <li><a href="index.php?controller=forum&action=show&id=<?= $post['id_forum'] ?>"><?= htmlspecialchars($forum->getTitre()) ?></a></li>
        <li class="separator"><i class="fas fa-chevron-right"></i></li>
        <li class="current">Post #<?= $post['id_post'] ?></li>
    </ul>
</nav>

<!-- Post Detail -->
<article class="post-card" style="margin-top: 1.5rem;">
    <div class="post-header">
        <div class="post-avatar role-<?= htmlspecialchars($post['auteur_role']) ?>">
            <?= strtoupper(substr($post['auteur_prenom'], 0, 1) . substr($post['auteur_nom'], 0, 1)) ?>
        </div>
        <div class="post-author-info">
            <span class="post-author-name">
                <?= htmlspecialchars($post['auteur_prenom'] . ' ' . $post['auteur_nom']) ?>
            </span>
            <span class="post-author-role role-<?= htmlspecialchars($post['auteur_role']) ?>">
                <?= htmlspecialchars($post['auteur_role']) ?>
            </span>
            <div class="post-date">
                <i class="fas fa-clock"></i> Publié le <?= date('d/m/Y à H:i', strtotime($post['date_publication'])) ?>
            </div>
        </div>
    </div>

    <div class="post-content full">
        <?= nl2br(htmlspecialchars($post['contenu'])) ?>
    </div>

    <!-- Réactions & Describe -->
    <div class="reaction-buttons">
        <button class="btn-react btn-like <?= ($post['user_reaction'] === 'like') ? 'active' : '' ?>" 
                data-type="like" data-id="<?= $post['id_post'] ?>" data-target="post">
            <i class="fas fa-thumbs-up"></i> <span class="count"><?= $post['likes'] ?? 0 ?></span> J'aime
        </button>
        <button class="btn-react btn-dislike <?= ($post['user_reaction'] === 'dislike') ? 'active' : '' ?>" 
                data-type="dislike" data-id="<?= $post['id_post'] ?>" data-target="post">
            <i class="fas fa-thumbs-down"></i> <span class="count"><?= $post['dislikes'] ?? 0 ?></span> Je n'aime pas
        </button>
        <button class="btn-react btn-describe" onclick="loadAndToggleDescribe(<?= $post['id_post'] ?>)">
            <i class="fas fa-chart-bar"></i> Analyser
        </button>
    </div>

    <!-- Panneau Décrire (Ajax) -->
    <div id="post-describe" class="describe-panel" style="display: none; background: #f8fafc; border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 1.5rem; margin-top: 1rem;">
        <div id="describe-loader" style="text-align: center; color: var(--text-muted); padding: 1rem;">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p style="margin-top: 0.5rem;">Analyse du contenu en cours...</p>
        </div>
        <div id="describe-content" style="display: none;">
            <h4 style="margin-bottom: 1rem; color: var(--text-color); font-size: 1.1rem;"><i class="fas fa-chart-pie" style="color: var(--accent-teal);"></i> Analyse Détaillée</h4>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <!-- Mots & Temps -->
                <div style="background: white; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; font-weight: 700;">Lecture</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-blue);"><span id="desc-words">0</span> <span style="font-size: 0.9rem; color: var(--text-color);">mots</span></div>
                    <div style="font-size: 0.85rem; color: var(--text-muted);"><i class="fas fa-clock"></i> ~<span id="desc-time">0</span> min</div>
                </div>
                
                <!-- Score de Qualité -->
                <div style="background: white; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; font-weight: 700;">Score d'Engagement</div>
                    <div style="display: flex; align-items: baseline; gap: 0.25rem;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-teal);" id="desc-score">0</div>
                        <div style="font-size: 0.9rem; color: var(--text-muted);">/ 100</div>
                    </div>
                    <div style="width: 100%; background: #e2e8f0; height: 6px; border-radius: 3px; margin-top: 0.5rem; overflow: hidden;">
                        <div id="desc-score-bar" style="width: 0%; height: 100%; background: var(--accent-teal); transition: width 1s ease;"></div>
                    </div>
                </div>
            </div>
            
            <!-- Mots Clés -->
            <div style="background: white; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; font-weight: 700; margin-bottom: 0.75rem;">Mots-clés principaux</div>
                <div id="desc-keywords" style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    <!-- Injected via JS -->
                </div>
            </div>
        </div>
    </div>
</article>

<!-- ===== COMMENTS SECTION ===== -->
<section class="comments-section">
    <h3>
        <i class="fas fa-comments" style="color: var(--accent-teal);"></i>
        Commentaires
        <span class="count-badge"><?= count($commentaires) ?></span>
    </h3>

    <!-- Session alerts (redirect feedback) -->
    <?php if (isset($_SESSION['comment_success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['comment_success']) ?>
        </div>
        <?php unset($_SESSION['comment_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['comment_errors'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars(implode(' ', $_SESSION['comment_errors'])) ?>
        </div>
        <?php unset($_SESSION['comment_errors']); ?>
    <?php endif; ?>

    <!-- Comment Form -->
    <?php if (isset($_SESSION['user'])): ?>
        <div class="form-card" style="margin-bottom: 2rem;">
            <form action="index.php?controller=commentaire&action=add" method="POST" data-validate="true">
                <input type="hidden" name="id_post" value="<?= $post['id_post'] ?>">

                <div class="form-group">
                    <label for="commentaire-contenu">
                        Votre commentaire <span class="required">*</span>
                    </label>
                    <textarea
                        id="commentaire-contenu"
                        name="contenu"
                        class="form-control"
                        rows="4"
                        placeholder="Partagez votre avis ou posez une question..."
                        data-required="true"
                        data-min="3"
                        data-max="2000"
                        data-label="Le commentaire"
                    ><?= htmlspecialchars($_SESSION['comment_contenu'] ?? '') ?></textarea>
                    <?php unset($_SESSION['comment_contenu']); ?>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Publier le commentaire
                </button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Comments List -->
    <?php if (!empty($commentaires)): ?>
        <?php foreach ($commentaires as $index => $c): ?>
            <div class="comment-item" style="animation-delay: <?= $index * 0.05 ?>s">
                <div class="comment-avatar role-<?= htmlspecialchars($c['auteur_role']) ?>"
                     style="background: <?= $c['auteur_role'] === 'professionnel' ? 'linear-gradient(135deg, #3b82f6, #14b8a6)' : 'linear-gradient(135deg, #8b5cf6, #3b82f6)' ?>">
                    <?= strtoupper(substr($c['auteur_prenom'], 0, 1) . substr($c['auteur_nom'], 0, 1)) ?>
                </div>
                <div class="comment-body">
                    <div class="comment-author">
                        <?= htmlspecialchars($c['auteur_prenom'] . ' ' . $c['auteur_nom']) ?>
                        <span class="post-author-role role-<?= htmlspecialchars($c['auteur_role']) ?>" style="font-size: 0.65rem;">
                            <?= htmlspecialchars($c['auteur_role']) ?>
                        </span>
                    </div>
                    <div class="comment-date">
                        <i class="fas fa-clock"></i> <?= date('d/m/Y à H:i', strtotime($c['date_commentaire'])) ?>
                    </div>
                    <div class="comment-text">
                        <?= nl2br(htmlspecialchars($c['contenu'])) ?>
                    </div>
                    
                    <div class="reaction-buttons" style="margin-top: 0.5rem; padding-top: 0.5rem;">
                        <button class="btn-react btn-like <?= ($c['user_reaction'] === 'like') ? 'active' : '' ?>" 
                                data-type="like" data-id="<?= $c['id_commentaire'] ?>" data-target="commentaire">
                            <i class="fas fa-thumbs-up"></i> <span class="count"><?= $c['likes'] ?? 0 ?></span>
                        </button>
                        <button class="btn-react btn-dislike <?= ($c['user_reaction'] === 'dislike') ? 'active' : '' ?>" 
                                data-type="dislike" data-id="<?= $c['id_commentaire'] ?>" data-target="commentaire">
                            <i class="fas fa-thumbs-down"></i> <span class="count"><?= $c['dislikes'] ?? 0 ?></span>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state" style="padding: 2rem;">
            <i class="fas fa-comment-slash"></i>
            <h3>Aucun commentaire</h3>
            <p>Soyez le premier à commenter ce post !</p>
        </div>
    <?php endif; ?>
</section>

<!-- AJAX Script for Reactions & Describe -->
<script>
async function loadAndToggleDescribe(idPost) {
    const panel = document.getElementById('post-describe');
    const loader = document.getElementById('describe-loader');
    const content = document.getElementById('describe-content');
    
    if (panel.style.display === 'block') {
        panel.style.display = 'none';
        return;
    }
    
    panel.style.display = 'block';
    if (content.style.display === 'block') return;
    
    loader.style.display = 'block';
    content.style.display = 'none';
    
    try {
        const response = await fetch('index.php?controller=post&action=describeAjax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_post: idPost })
        });
        
        const res = await response.json();
        
        if (res.success) {
            const data = res.data;
            
            document.getElementById('desc-words').textContent = data.word_count;
            document.getElementById('desc-time').textContent = data.reading_time;
            document.getElementById('desc-score').textContent = data.score;
            setTimeout(() => {
                document.getElementById('desc-score-bar').style.width = data.score + '%';
            }, 100);
            
            const kwContainer = document.getElementById('desc-keywords');
            kwContainer.innerHTML = '';
            if (data.keywords && data.keywords.length > 0) {
                data.keywords.forEach(kw => {
                    const badge = document.createElement('span');
                    badge.style.background = '#e0f2fe';
                    badge.style.color = '#0284c7';
                    badge.style.padding = '0.25rem 0.75rem';
                    badge.style.borderRadius = '100px';
                    badge.style.fontSize = '0.75rem';
                    badge.style.fontWeight = '600';
                    badge.textContent = kw;
                    kwContainer.appendChild(badge);
                });
            } else {
                kwContainer.innerHTML = '<span style="color: var(--text-muted); font-size: 0.85rem;">Aucun mot-clé trouvé.</span>';
            }
            
            loader.style.display = 'none';
            content.style.display = 'block';
        } else {
            loader.innerHTML = '<div style="color: var(--accent-red);"><i class="fas fa-exclamation-triangle"></i> ' + (res.message || 'Erreur d\'analyse.') + '</div>';
        }
    } catch (err) {
        console.error(err);
        loader.innerHTML = '<div style="color: var(--accent-red);"><i class="fas fa-exclamation-triangle"></i> Erreur réseau.</div>';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const reactButtons = document.querySelectorAll('.btn-react[data-type]');
    
    reactButtons.forEach(btn => {
        btn.addEventListener('click', async function() {
            const type = this.getAttribute('data-type');
            const targetId = this.getAttribute('data-id');
            const targetType = this.getAttribute('data-target'); // 'post' or 'commentaire'
            
            const isUserLoggedIn = <?= isset($_SESSION['user']) ? 'true' : 'false' ?>;
            if (!isUserLoggedIn) {
                alert('Veuillez vous connecter pour réagir.');
                return;
            }

            const url = targetType === 'post' 
                ? 'index.php?controller=post&action=react' 
                : 'index.php?controller=commentaire&action=react';

            const payload = {
                type: type
            };
            if (targetType === 'post') payload.id_post = targetId;
            else payload.id_commentaire = targetId;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update current container buttons
                    const container = this.closest('.reaction-buttons');
                    const btnLike = container.querySelector('.btn-like');
                    const btnDislike = container.querySelector('.btn-dislike');
                    
                    // Update counts
                    btnLike.querySelector('.count').textContent = data.likes;
                    btnDislike.querySelector('.count').textContent = data.dislikes;
                    
                    // Update active state
                    btnLike.classList.remove('active');
                    btnDislike.classList.remove('active');
                    
                    if (data.action === 'added' || data.action === 'updated') {
                        if (type === 'like') btnLike.classList.add('active');
                        else btnDislike.classList.add('active');
                    }
                } else {
                    alert(data.message || 'Une erreur est survenue.');
                }
            } catch (err) {
                console.error(err);
                alert('Erreur réseau. Impossible d\'enregistrer la réaction.');
            }
        });
    });
});
</script>

<?php require __DIR__ . '/../../layout/front_footer.php'; ?>
