(function () {

    var meds       = window.MEDICAMENTS_DATA || [];
    var messagesEl = document.getElementById('chat-messages');
    var inputEl    = document.getElementById('chat-input');
    var sendBtn    = document.getElementById('chat-send');

    var SYMPTOM_MAP = [
        {
            keys:   ['fièvre','fievre','température','temperature','chaud','frisson','chaleur'],
            label:  'fièvre',
            search: ['antalgique','douleur','doliprane','ibuprofène','comprimé']
        },
        {
            keys:   ['douleur','douleurs','mal','maux','migraine','tête','tete','céphalée'],
            label:  'douleurs',
            search: ['antalgique','douleur','doliprane','ibuprofène','comprimé']
        },
        {
            keys:   ['toux','gorge','bronchite','irritation','enrouement'],
            label:  'toux / gorge',
            search: ['toux','gorge','sirop','irritation','toplexil']
        },
        {
            keys:   ['infection','antibiotique','angine','otite','sinusite','bactérie'],
            label:  'infection',
            search: ['antibiotique','infection','amoxicilline','gélule']
        },
        {
            keys:   ['diarrhée','diarrhee','digestion','ventre','estomac','nausée','vomissement'],
            label:  'troubles digestifs',
            search: ['diarrhée','digestif','smecta','sachet','intestinal']
        },
        {
            keys:   ['inflammation','gonflement','articulation','rhumatisme'],
            label:  'inflammation',
            search: ['anti-inflammatoire','inflammation','ibuprofène']
        }
    ];

    var GREETINGS = ['bonjour','salut','bonsoir','hello','coucou'];
    var THANKS    = ['merci','super','parfait','excellent'];
    var FAREWELLS = ['au revoir','bye','bonne journée','à bientôt'];

    function norm(s) {
        return String(s).toLowerCase()
            .normalize('NFD').replace(/[̀-ͯ]/g, '')
            .replace(/[^a-z0-9\s]/g, ' ').replace(/\s+/g, ' ').trim();
    }
    function has(haystack, needle) {
        return norm(haystack).indexOf(norm(needle)) !== -1;
    }
    function esc(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }
    function cut(s, n) {
        s = String(s);
        return s.length > n ? s.slice(0, n) + '…' : s;
    }

    function searchMeds(terms) {
        var seen   = {};
        var result = [];

        meds.forEach(function (m) {
            var id = String(m.id);
            if (seen[id]) return;
            var score = 0;
            terms.forEach(function (t) {
                if (has(m.nom,         t)) score += 4;
                if (has(m.forme,       t)) score += 2;
                if (has(m.description, t)) score += 1;
            });
            if (score > 0) { seen[id] = true; result.push({ med: m, score: score }); }
        });

        return result
            .sort(function (a, b) { return b.score - a.score; })
            .slice(0, 3)
            .map(function (r) { return r.med; });
    }

    function detectSymptoms(input) {
        var ni      = norm(input);
        var labels  = [];
        var search  = [];
        var usedKeys = {};

        SYMPTOM_MAP.forEach(function (s) {
            var hit = s.keys.some(function (k) { return ni.indexOf(norm(k)) !== -1; });
            if (hit) {
                labels.push(s.label);
                s.search.forEach(function (t) {
                    if (!usedKeys[t]) { usedKeys[t] = true; search.push(t); }
                });
            }
        });

        if (!search.length) {
            norm(input).split(' ')
                .filter(function (w) { return w.length > 3; })
                .forEach(function (w) { search.push(w); });
        }

        return { labels: labels, search: search };
    }

    function getResponse(input) {
        var ni = norm(input);

        if (GREETINGS.some(function (g) { return ni.indexOf(g) !== -1; })) {
            return { text: 'Bonjour ! Décrivez vos symptômes et je recherche dans notre catalogue de médicaments.', meds: [] };
        }
        if (THANKS.some(function (g) { return ni.indexOf(g) !== -1; })) {
            return { text: 'De rien ! N\'hésitez pas si vous avez d\'autres questions. 😊', meds: [] };
        }
        if (FAREWELLS.some(function (g) { return ni.indexOf(g) !== -1; })) {
            return { text: 'Au revoir ! Consultez un médecin si vos symptômes persistent. 👋', meds: [] };
        }

        var detected = detectSymptoms(input);
        var results  = searchMeds(detected.search);

        if (!results.length) {
            return {
                text: 'Je n\'ai pas trouvé de médicament correspondant. Essayez : <em>fièvre, toux, douleur, infection, digestion…</em>',
                meds: []
            };
        }

        var intro = detected.labels.length
            ? 'Pour les symptômes de <strong>' + detected.labels.join(' et ') + '</strong> :'
            : 'Voici les résultats pour votre recherche :';

        return { text: intro, meds: results };
    }

    function stockBadge(stock) {
        stock = parseInt(stock, 10) || 0;
        if (stock > 100) return '<span style="background:#dcfce7;color:#166534;font-size:10px;padding:2px 7px;border-radius:100px;font-weight:600;">En stock</span>';
        if (stock > 20)  return '<span style="background:#fef9c3;color:#854d0e;font-size:10px;padding:2px 7px;border-radius:100px;font-weight:600;">Stock limité</span>';
        return '<span style="background:#fee2e2;color:#991b1b;font-size:10px;padding:2px 7px;border-radius:100px;font-weight:600;">Stock faible</span>';
    }

    function buildCards(results) {
        var html = '<div class="ai-cards">';
        results.forEach(function (m) {
            html +=
                '<a class="ai-card" href="index.php?action=show_medicament&id=' + m.id + '">' +
                    '<div class="ai-card-top">' +
                        '<div class="ai-card-icon">💊</div>' +
                        '<div class="ai-card-info">' +
                            '<div class="ai-card-nom">' + esc(m.nom) + '</div>' +
                            '<div class="ai-card-meta">' +
                                (m.dosage ? '<span class="ai-tag dosage">' + esc(m.dosage) + '</span>' : '') +
                                (m.forme  ? '<span class="ai-tag forme">'  + esc(m.forme)  + '</span>' : '') +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<p class="ai-card-desc">' + esc(cut(m.description, 80)) + '</p>' +
                    '<div class="ai-card-footer">' +
                        '<span class="ai-card-prix">' + parseFloat(m.prix).toFixed(2) + ' DT</span>' +
                        stockBadge(m.stock) +
                    '</div>' +
                '</a>';
        });
        html += '</div>';
        return html;
    }

    function addMsg(who, htmlContent, hasCards) {
        var row = document.createElement('div');
        row.className = 'cm-row ' + who;
        if (hasCards) row.className += ' has-cards';

        var avatar = document.createElement('div');
        avatar.className = 'cm-avatar';
        avatar.textContent = who === 'bot' ? '💊' : '🧑';

        var bubble = document.createElement('div');
        bubble.className = 'cm-bubble';
        bubble.innerHTML = htmlContent;

        if (who === 'bot') { row.appendChild(avatar); row.appendChild(bubble); }
        else               { row.appendChild(bubble); row.appendChild(avatar); }

        messagesEl.appendChild(row);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function addTyping() {
        var row = document.createElement('div');
        row.className = 'cm-row bot';
        row.id = 'cm-typing';
        row.innerHTML =
            '<div class="cm-avatar">💊</div>' +
            '<div class="cm-bubble cm-typing"><span></span><span></span><span></span></div>';
        messagesEl.appendChild(row);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function send(text) {
        text = text.trim();
        if (!text) return;

        addMsg('user', '<p>' + esc(text) + '</p>', false);
        inputEl.value = '';
        sendBtn.disabled = true;
        addTyping();

        setTimeout(function () {
            var t = document.getElementById('cm-typing');
            if (t) t.remove();

            var resp = getResponse(text);
            var html = '<p>' + resp.text + '</p>';
            if (resp.meds.length) html += buildCards(resp.meds);

            addMsg('bot', html, resp.meds.length > 0);
            sendBtn.disabled = false;
            inputEl.focus();
        }, 600);
    }

    sendBtn.addEventListener('click', function () { send(inputEl.value); });
    inputEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); send(inputEl.value); }
    });
    document.querySelectorAll('.chat-chip').forEach(function (chip) {
        chip.addEventListener('click', function () { send(chip.dataset.msg || chip.textContent); });
    });

})();
