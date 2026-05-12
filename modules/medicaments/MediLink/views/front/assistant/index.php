<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="listing-hero section-block">
    <h1>Assistant pharmacien</h1>
    <p>Décrivez vos symptômes en français — l'assistant vous recommande les médicaments disponibles.</p>
</div>

<div class="section-block">
    <div class="chat-shell">

        <!-- Zone messages -->
        <div class="chat-messages" id="chat-messages">
            <div class="cm-row bot">
                <div class="cm-avatar">💊</div>
                <div class="cm-bubble">
                    <p>Bonjour ! Je suis votre assistant pharmacien.</p>
                    <p>Décrivez vos <strong>symptômes</strong> et je vous recommande les médicaments disponibles dans notre catalogue.</p>
                    <div class="chat-suggestions">
                        <span class="chat-chip" data-msg="J'ai de la fièvre">🌡 Fièvre</span>
                        <span class="chat-chip" data-msg="J'ai mal à la tête">🤕 Mal de tête</span>
                        <span class="chat-chip" data-msg="J'ai de la toux">😮‍💨 Toux</span>
                        <span class="chat-chip" data-msg="J'ai une infection">🦠 Infection</span>
                        <span class="chat-chip" data-msg="J'ai des problèmes digestifs">🤢 Digestion</span>
                        <span class="chat-chip" data-msg="J'ai des douleurs">💊 Douleurs</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Zone saisie -->
        <div class="chat-input-row">
            <input type="text" id="chat-input"
                   placeholder="Ex : j'ai de la fièvre et des maux de tête…"
                   autocomplete="off" maxlength="200">
            <button type="button" id="chat-send">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </div>

        <p class="chat-disclaimer">
            ⚠ Cet assistant est informatif. Consultez toujours un médecin ou pharmacien avant de prendre un médicament.
        </p>
    </div>
</div>

<script>
var MEDICAMENTS_DATA = <?= json_encode(array_values($medicaments), JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="js/assistant.js"></script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
