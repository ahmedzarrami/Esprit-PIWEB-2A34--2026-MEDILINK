<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
/* ── Page wrapper ── */
.asst-page { padding: 28px 0 48px; }

/* ── Two-column layout ── */
.asst-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 24px;
    align-items: start;
}

/* ══════════════════════════════
   SIDEBAR
══════════════════════════════ */
.asst-sidebar {
    display: flex;
    flex-direction: column;
    gap: 16px;
    position: sticky;
    top: 80px;
}

/* Profile card */
.asst-profile {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    border-radius: 16px;
    padding: 24px 20px;
    color: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 14px;
}
.asst-avatar-lg {
    width: 68px; height: 68px;
    border-radius: 50%;
    background: rgba(255,255,255,.18);
    border: 2px solid rgba(255,255,255,.4);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.asst-avatar-lg svg { width: 34px; height: 34px; }
.asst-profile-name {
    font-size: 15px; font-weight: 700;
    line-height: 1.3;
}
.asst-profile-role {
    font-size: 12px;
    opacity: .75;
    margin-top: 2px;
}
.asst-online {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 100px;
    padding: 3px 10px;
    font-size: 11px; font-weight: 600;
}
.asst-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #4ade80;
    box-shadow: 0 0 0 2px rgba(74,222,128,.3);
    animation: asst-pulse 2s infinite;
}
@keyframes asst-pulse {
    0%,100% { box-shadow: 0 0 0 2px rgba(74,222,128,.3); }
    50%      { box-shadow: 0 0 0 5px rgba(74,222,128,.1); }
}

/* Stats strip */
.asst-stats {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
}
.asst-stat {
    text-align: center;
    padding: 6px 8px;
}
.asst-stat + .asst-stat {
    border-left: 1px solid #e2e8f0;
}
.asst-stat-num {
    display: block;
    font-size: 22px; font-weight: 800;
    color: #1e3a8a;
    line-height: 1.1;
}
.asst-stat-label {
    display: block;
    font-size: 10px; font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-top: 2px;
}

/* Quick topics */
.asst-topics {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
}
.asst-topics-title {
    padding: 12px 16px 10px;
    font-size: 11px; font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .06em;
    border-bottom: 1px solid #f1f5f9;
}
.asst-topic {
    width: 100%;
    display: flex; align-items: center; gap: 10px;
    padding: 10px 16px;
    background: none; border: none;
    border-bottom: 1px solid #f8fafc;
    cursor: pointer;
    font-size: 13px; font-weight: 500;
    color: #334155;
    text-align: left;
    transition: background .12s, color .12s;
}
.asst-topic:last-child { border-bottom: none; }
.asst-topic:hover { background: #f0f7ff; color: #2563eb; }
.asst-topic-icon {
    font-size: 16px;
    width: 28px; height: 28px;
    background: #f1f5f9;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: background .12s;
}
.asst-topic:hover .asst-topic-icon { background: #dbeafe; }

/* Sidebar disclaimer */
.asst-notice {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 12px;
    padding: 12px 14px;
    font-size: 11.5px;
    color: #92400e;
    line-height: 1.55;
    display: flex; gap: 8px;
}
.asst-notice svg { flex-shrink: 0; margin-top: 1px; }

/* ══════════════════════════════
   MAIN CHAT PANEL
══════════════════════════════ */
.asst-main {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    box-shadow: 0 4px 24px rgba(15,23,42,.06);
    display: flex;
    flex-direction: column;
    height: 680px;
    overflow: hidden;
}

/* Chat header bar */
.asst-chat-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    background: #fafbff;
    flex-shrink: 0;
}
.asst-chat-header-left { display: flex; align-items: center; gap: 12px; }
.asst-avatar-sm {
    width: 40px; height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #fff;
    font-size: 13px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    letter-spacing: .5px;
    flex-shrink: 0;
}
.asst-chat-title {
    font-size: 14px; font-weight: 700;
    color: #0f172a;
}
.asst-chat-sub {
    font-size: 11.5px; color: #64748b;
    margin-top: 1px;
}
.asst-badge {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
    font-size: 10.5px; font-weight: 700;
    padding: 4px 10px; border-radius: 100px;
    letter-spacing: .02em;
}

/* Messages area — override the old .chat-messages height */
.asst-main .chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    height: auto;
}

/* Bot avatar inside chat — replace emoji style */
.asst-main .cm-avatar {
    width: 34px; height: 34px;
    border-radius: 10px;
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #fff;
    font-size: 11px; font-weight: 800;
    letter-spacing: .4px;
}
.asst-main .cm-row.user .cm-avatar {
    background: linear-gradient(135deg, #7c3aed, #a855f7);
    border-radius: 50%;
    font-size: 13px;
}

/* Input bar */
.asst-input-bar {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 18px;
    border-top: 1px solid #f1f5f9;
    background: #fafbff;
    flex-shrink: 0;
}
.asst-input-bar input {
    flex: 1;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 11px 16px;
    font-size: 13.5px;
    font-family: inherit;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    background: #fff;
}
.asst-input-bar input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.08);
}
.asst-input-bar input::placeholder { color: #94a3b8; }
#chat-send {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #fff;
    border: none;
    border-radius: 12px;
    width: 46px; height: 46px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: opacity .15s, transform .1s;
    flex-shrink: 0;
}
#chat-send:hover:not(:disabled) { opacity: .9; transform: scale(1.04); }
#chat-send:disabled { opacity: .45; cursor: not-allowed; }

/* Sub-disclaimer inside input bar */
.asst-input-hint {
    font-size: 10.5px; color: #94a3b8;
    text-align: center;
    padding: 0 18px 10px;
    background: #fafbff;
}

/* ── Responsive ── */
@media (max-width: 900px) {
    .asst-layout {
        grid-template-columns: 1fr;
    }
    .asst-sidebar {
        position: static;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .asst-profile { grid-column: 1 / -1; }
    .asst-main { height: 580px; }
}
@media (max-width: 600px) {
    .asst-sidebar { grid-template-columns: 1fr; }
    .asst-main { height: 520px; border-radius: 14px; }
    .asst-topics { display: none; }
}
</style>

<div class="section-block asst-page">

    <!-- Page title -->
    <div style="margin-bottom:24px">
        <h1 style="font-size:22px;font-weight:800;color:#0f172a;margin-bottom:4px">Assistant pharmacien</h1>
        <p style="color:#64748b;font-size:14px">Décrivez vos symptômes — l'assistant recherche dans notre catalogue de médicaments.</p>
    </div>

    <div class="asst-layout">

        <!-- ═══ SIDEBAR ═══ -->
        <aside class="asst-sidebar">

            <!-- Bot profile -->
            <div class="asst-profile">
                <div class="asst-avatar-lg">
                    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="17" y="6" width="6" height="28" rx="3" fill="white"/>
                        <rect x="6" y="17" width="28" height="6" rx="3" fill="white"/>
                    </svg>
                </div>
                <div>
                    <div class="asst-profile-name">Assistant Pharmacien</div>
                    <div class="asst-profile-role">MediLink · Recommandations médicaments</div>
                </div>
                <div class="asst-online">
                    <span class="asst-dot"></span>
                    En ligne
                </div>
            </div>

            <!-- Stats -->
            <div class="asst-stats">
                <div class="asst-stat">
                    <span class="asst-stat-num" id="med-count"><?= count($medicaments) ?></span>
                    <span class="asst-stat-label">Médicaments</span>
                </div>
                <div class="asst-stat">
                    <span class="asst-stat-num">24/7</span>
                    <span class="asst-stat-label">Disponible</span>
                </div>
            </div>

            <!-- Quick topics -->
            <div class="asst-topics">
                <div class="asst-topics-title">Symptômes fréquents</div>
                <button class="asst-topic" data-msg="J'ai de la fièvre">
                    <span class="asst-topic-icon">🌡️</span>Fièvre &amp; Température
                </button>
                <button class="asst-topic" data-msg="J'ai mal à la tête">
                    <span class="asst-topic-icon">🤕</span>Migraine &amp; Maux de tête
                </button>
                <button class="asst-topic" data-msg="J'ai de la toux">
                    <span class="asst-topic-icon">😮‍💨</span>Toux &amp; Gorge
                </button>
                <button class="asst-topic" data-msg="J'ai une infection">
                    <span class="asst-topic-icon">🦠</span>Infection &amp; Antibiotiques
                </button>
                <button class="asst-topic" data-msg="J'ai des problèmes digestifs">
                    <span class="asst-topic-icon">🤢</span>Troubles digestifs
                </button>
                <button class="asst-topic" data-msg="J'ai des douleurs">
                    <span class="asst-topic-icon">💊</span>Douleurs &amp; Antalgiques
                </button>
                <button class="asst-topic" data-msg="J'ai une inflammation">
                    <span class="asst-topic-icon">🔥</span>Inflammation
                </button>
            </div>

            <!-- Notice -->
            <div class="asst-notice">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#b45309" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Cet assistant est purement informatif. Consultez toujours un médecin ou pharmacien avant de prendre tout médicament.
            </div>

        </aside>

        <!-- ═══ MAIN CHAT ═══ -->
        <main class="asst-main">

            <!-- Header -->
            <div class="asst-chat-header">
                <div class="asst-chat-header-left">
                    <div class="asst-avatar-sm">Rx</div>
                    <div>
                        <div class="asst-chat-title">Assistant pharmacien</div>
                        <div class="asst-chat-sub">Recommandations basées sur notre catalogue</div>
                    </div>
                </div>
                <span class="asst-badge">✓ Catalogue local</span>
            </div>

            <!-- Messages -->
            <div class="chat-messages" id="chat-messages">
                <div class="cm-row bot">
                    <div class="cm-avatar" style="display:flex;align-items:center;justify-content:center">Rx</div>
                    <div class="cm-bubble">
                        <p>Bonjour ! Je suis votre <strong>assistant pharmacien</strong>.</p>
                        <p>Décrivez vos <strong>symptômes</strong> et je vous recommande les médicaments disponibles dans notre catalogue. Vous pouvez aussi utiliser les raccourcis à gauche.</p>
                        <div class="chat-suggestions">
                            <span class="chat-chip" data-msg="J'ai de la fièvre">🌡 Fièvre</span>
                            <span class="chat-chip" data-msg="J'ai mal à la tête">🤕 Maux de tête</span>
                            <span class="chat-chip" data-msg="J'ai de la toux">😮‍💨 Toux</span>
                            <span class="chat-chip" data-msg="J'ai une infection">🦠 Infection</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input -->
            <div class="asst-input-bar">
                <input type="text" id="chat-input"
                       placeholder="Ex : j'ai de la fièvre et des maux de tête…"
                       autocomplete="off" maxlength="200">
                <button type="button" id="chat-send" title="Envoyer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                </button>
            </div>
            <div class="asst-input-hint">Appuyez sur Entrée pour envoyer</div>

        </main>

    </div>
</div>

<script>
var MEDICAMENTS_DATA = <?= json_encode(array_values($medicaments), JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="js/assistant.js"></script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
