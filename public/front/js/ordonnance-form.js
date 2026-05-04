(function () {

    var data      = window.MEDICAMENTS_DATA || [];
    var container = document.getElementById('lignes-container');
    var emptyMsg  = document.getElementById('lignes-empty');
    var addInput  = document.getElementById('med-add-input');
    var addDrop   = document.getElementById('med-add-dropdown');

    /* ══════════════════════════════════════
       FEEDBACK VISUEL
    ══════════════════════════════════════ */
    function setValid(el) {
        el.style.borderColor  = '#0da271';
        el.style.boxShadow    = '0 0 0 3px rgba(13,162,113,.12)';
        el.style.paddingRight = '36px';
        setIcon(el, '✓', '#0da271');
        var s = msgEl(el); if (s) { s.textContent = ''; s.style.display = 'none'; }
    }
    function setInvalid(el, msg) {
        el.style.borderColor  = '#dc2626';
        el.style.boxShadow    = '0 0 0 3px rgba(220,38,38,.12)';
        el.style.paddingRight = '36px';
        setIcon(el, '✗', '#dc2626');
        var s = msgEl(el);
        if (!s) {
            s = document.createElement('span');
            s.className = 'field-error';
            el.parentNode.appendChild(s);
        }
        s.textContent = msg;
        s.style.display = 'block';
        s.style.cssText = 'display:block;font-size:11px;color:#dc2626;font-weight:500;margin-top:4px';
    }
    function setNeutral(el) {
        el.style.borderColor  = '';
        el.style.boxShadow    = '';
        el.style.paddingRight = '';
        removeIcon(el);
        var s = msgEl(el); if (s) { s.textContent = ''; s.style.display = 'none'; }
    }
    function msgEl(el) { return el.parentNode.querySelector('.field-error'); }

    function setIcon(el, char, color) {
        removeIcon(el);
        var wrap = el.parentNode;
        if (getComputedStyle(wrap).position === 'static') wrap.style.position = 'relative';
        var ic = document.createElement('span');
        ic.className = 'val-icon';
        ic.textContent = char;
        ic.style.cssText = 'position:absolute;right:10px;top:50%;transform:translateY(-50%);' +
            'font-size:14px;font-weight:700;color:'+color+';pointer-events:none;';
        var lbl = wrap.querySelector('label');
        if (lbl) ic.style.top = 'calc(50% + '+(lbl.offsetHeight/2)+'px)';
        wrap.appendChild(ic);
    }
    function removeIcon(el) {
        var ic = el.parentNode.querySelector('.val-icon');
        if (ic) ic.remove();
    }

    function clearAll() {
        document.querySelectorAll('.field-error').forEach(function(s){ s.style.display='none'; s.textContent=''; });
        document.querySelectorAll('.val-icon').forEach(function(i){ i.remove(); });
        document.querySelectorAll('input,select,textarea').forEach(function(e){
            e.style.borderColor = ''; e.style.boxShadow = ''; e.style.paddingRight = '';
        });
        var b = document.getElementById('js-error-banner'); if (b) b.remove();
    }

    function showBanner(msg) {
        var b = document.getElementById('js-error-banner'); if (b) b.remove();
        b = document.createElement('div'); b.id = 'js-error-banner'; b.className = 'alert-errors';
        b.innerHTML = '<strong>⚠ ' + msg + '</strong>';
        var form = document.getElementById('ordonnance-form');
        form.parentNode.insertBefore(b, form);
        b.scrollIntoView({ behavior:'smooth', block:'center' });
    }

    /* ══════════════════════════════════════
       CONTRÔLE MÉTIER — BADGES D'ALERTE
    ══════════════════════════════════════ */
    function appendBadge(card, msg, bg, border, color, cls) {
        var badge = document.createElement('div');
        badge.className = cls || 'metier-warn';
        badge.innerHTML = msg;
        badge.style.cssText = 'background:' + bg + ';border:1px solid ' + border + ';color:' + color + ';' +
            'font-size:12px;padding:6px 12px;border-radius:8px;margin-top:8px;font-weight:500;line-height:1.5;';
        card.appendChild(badge);
    }

    function updateCardWarnings(card) {
        card.querySelectorAll('.metier-warn,.pediatric-warn').forEach(function(w) { w.remove(); });

        var qteEl = card.querySelector('input[name*="quantite"]');
        var durEl = card.querySelector('input[name*="duree"]');

        if (qteEl) {
            var qte = parseInt(qteEl.value, 10) || 0;
            if (qte > 15) {
                appendBadge(card,
                    '⚠ Quantité élevée (' + qte + ' unités) — vérifiez si c\'est intentionnel',
                    '#fffbeb', '#f59e0b', '#92400e');
            }
        }

        if (durEl && durEl.value.trim()) {
            var match = durEl.value.match(/(\d+)\s*(jour|semaine|mois)/i);
            if (match) {
                var n    = parseInt(match[1], 10);
                var unit = match[2].toLowerCase();
                var days = unit.indexOf('jour') === 0 ? n
                         : unit.indexOf('sem')  === 0 ? n * 7
                         : n * 30;
                if (days > 60) {
                    appendBadge(card,
                        '⚠ Durée longue (' + durEl.value.trim() + ') — prescription étendue, vérifiez',
                        '#fffbeb', '#f59e0b', '#92400e');
                }
            }
        }

        var ageEl = document.getElementById('patient_age');
        if (ageEl && ageEl.value.trim()) {
            var age = parseInt(ageEl.value, 10);
            if (!isNaN(age) && age >= 0 && age < 12) {
                appendBadge(card,
                    '👶 Patient pédiatrique (' + age + ' ans) — vérifier la posologie adaptée à l\'âge',
                    '#eff4ff', '#1a56db', '#1e3a8a', 'pediatric-warn');
            }
        }
    }

    function refreshAllWarnings() {
        container.querySelectorAll('.ligne-card').forEach(updateCardWarnings);
    }

    /* ══════════════════════════════════════
       DÉTECTION DE DOUBLONS — MODAL
    ══════════════════════════════════════ */
    function getExistingCard(medId) {
        var found = null;
        container.querySelectorAll('.ligne-card').forEach(function(card) {
            var h = card.querySelector('.med-hidden-id');
            if (h && parseInt(h.value, 10) === medId) found = card;
        });
        return found;
    }

    function showDuplicateDialog(med, existingCard, callback) {
        var overlay = document.createElement('div');
        overlay.style.cssText =
            'position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:10000;' +
            'display:flex;align-items:center;justify-content:center;';

        var box = document.createElement('div');
        box.style.cssText =
            'background:#fff;border-radius:16px;padding:32px;max-width:460px;width:90%;' +
            'box-shadow:0 25px 60px rgba(0,0,0,.3);';

        box.innerHTML =
            '<div style="text-align:center;margin-bottom:20px;">' +
                '<div style="font-size:44px;margin-bottom:10px;">⚠️</div>' +
                '<h3 style="margin:0 0 10px;font-size:18px;font-weight:700;color:#0f172a;">' +
                    'Médicament déjà prescrit' +
                '</h3>' +
                '<p style="margin:0;color:#64748b;font-size:14px;line-height:1.6;">' +
                    '<strong style="color:#0f172a;">' + esc(med.nom) + '</strong>' +
                    (med.dosage
                        ? '&nbsp;<span style="background:#f1f5f9;color:#475569;padding:2px 8px;' +
                          'border-radius:100px;font-size:12px;">' + esc(med.dosage) + '</span>'
                        : '') +
                    ' figure déjà dans cette ordonnance.<br>Que souhaitez-vous faire ?' +
                '</p>' +
            '</div>' +
            '<div style="display:grid;gap:10px;">' +
                '<button class="dup-merge" style="background:#1a56db;color:#fff;border:none;' +
                    'border-radius:10px;padding:13px 20px;font-size:14px;font-weight:600;' +
                    'cursor:pointer;text-align:left;">' +
                    '🔗&nbsp; <strong>Fusionner</strong> — ajouter 1 à la quantité existante' +
                '</button>' +
                '<button class="dup-add" style="background:#f8fafc;color:#475569;' +
                    'border:1px solid #e2e8f0;border-radius:10px;padding:13px 20px;' +
                    'font-size:14px;font-weight:500;cursor:pointer;text-align:left;">' +
                    '➕&nbsp; Ajouter en doublon <span style="color:#94a3b8;font-size:12px;">(non recommandé)</span>' +
                '</button>' +
                '<button class="dup-cancel" style="background:#fff;color:#94a3b8;' +
                    'border:1px solid #f1f5f9;border-radius:10px;padding:11px 20px;' +
                    'font-size:13px;cursor:pointer;">' +
                    'Annuler' +
                '</button>' +
            '</div>';

        overlay.appendChild(box);
        document.body.appendChild(overlay);

        function close() { overlay.remove(); }

        box.querySelector('.dup-merge').addEventListener('click', function() {
            close();
            var qteEl = existingCard.querySelector('input[name*="quantite"]');
            if (qteEl) {
                qteEl.value = Math.min(999, (parseInt(qteEl.value, 10) || 0) + 1);
                validate(qteEl, 'quantite');
                updateCardWarnings(existingCard);
            }
            existingCard.style.outline    = '3px solid #1a56db';
            existingCard.style.transition = 'outline .3s';
            existingCard.scrollIntoView({ behavior:'smooth', block:'center' });
            setTimeout(function() { existingCard.style.outline = ''; }, 2500);
            callback('merge');
        });

        box.querySelector('.dup-add').addEventListener('click', function() {
            close(); callback('add');
        });

        box.querySelector('.dup-cancel').addEventListener('click', function() {
            close(); callback('cancel');
        });

        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) { close(); callback('cancel'); }
        });
    }

    /* ══════════════════════════════════════
       RÈGLES DE VALIDATION
    ══════════════════════════════════════ */
    var RULES = {
        patient_nom: function(el) {
            var v = el.value.trim();
            if (!v)             return 'Le nom du patient est obligatoire.';
            if (v.length < 2)   return 'Minimum 2 caractères.';
            if (v.length > 100) return 'Maximum 100 caractères.';
            if (!/^[\p{L}\s'\-\.]+$/u.test(v)) return 'Lettres, espaces, tirets et apostrophes uniquement.';
            return null;
        },
        patient_age: function(el) {
            var v = el.value.trim();
            if (!v) return null;
            if (!/^\d+$/.test(v)) return 'L\'âge doit être un nombre entier.';
            var n = parseInt(v, 10);
            if (n < 0 || n > 130) return 'Âge invalide (entre 0 et 130).';
            return null;
        },
        date_ordonnance: function(el) {
            if (!el.value) return 'La date est obligatoire.';
            var d = new Date(el.value);
            if (isNaN(d.getTime())) return 'Date invalide.';
            var maxDate = new Date(); maxDate.setFullYear(maxDate.getFullYear() + 1);
            if (d > maxDate) return 'La date ne peut pas dépasser un an dans le futur.';
            return null;
        },
        posologie: function(el) {
            var v = el.value.trim();
            if (!v)             return 'La posologie est obligatoire.';
            if (v.length < 3)   return 'Minimum 3 caractères (ex : 1 cp matin).';
            if (v.length > 200) return 'Maximum 200 caractères.';
            return null;
        },
        duree: function(el) {
            var v = el.value.trim();
            if (!v) return null;
            if (v.length > 50) return 'Maximum 50 caractères.';
            return null;
        },
        quantite: function(el) {
            var v = el.value.trim();
            if (!v || v === '0')    return 'La quantité doit être au moins 1.';
            if (!/^\d+$/.test(v))   return 'Nombre entier requis.';
            if (parseInt(v, 10) < 1)   return 'Minimum 1.';
            if (parseInt(v, 10) > 999) return 'Maximum 999.';
            return null;
        }
    };

    function validate(el, ruleName) {
        var rule = RULES[ruleName];
        if (!rule) return true;
        var err = rule(el);
        if (err) { setInvalid(el, err); return false; }
        setValid(el); return true;
    }

    function attachLive(el, ruleName, event) {
        el.addEventListener(event || 'input', function() { validate(el, ruleName); });
        el.addEventListener('blur', function() {
            if (el.value.trim() !== '') validate(el, ruleName);
        });
    }

    /* ══════════════════════════════════════
       CHAMPS PRINCIPAUX
    ══════════════════════════════════════ */
    var nomEl  = document.getElementById('patient_nom');
    var ageEl  = document.getElementById('patient_age');
    var dateEl = document.getElementById('date_ordonnance');

    if (nomEl)  attachLive(nomEl,  'patient_nom');
    if (ageEl) {
        attachLive(ageEl, 'patient_age');
        ageEl.addEventListener('input',  refreshAllWarnings);
        ageEl.addEventListener('change', refreshAllWarnings);
    }
    if (dateEl) attachLive(dateEl, 'date_ordonnance', 'change');

    if (nomEl) {
        nomEl.addEventListener('keypress', function(e) {
            var ch = String.fromCharCode(e.charCode);
            if (!/[\p{L}\s'\-\.]/u.test(ch)) e.preventDefault();
        });
    }

    if (ageEl) {
        ageEl.addEventListener('keypress', function(e) {
            if (!/\d/.test(String.fromCharCode(e.charCode))) e.preventDefault();
        });
    }

    /* ══════════════════════════════════════
       AUTOCOMPLETE
    ══════════════════════════════════════ */
    function norm(s) {
        return String(s).toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,'');
    }

    function buildDropdown(input, dropEl, onSelect) {
        Object.assign(dropEl.style, {
            display:'none', position:'absolute', top:'100%', left:'0', right:'0',
            marginTop:'4px', background:'#fff', border:'1px solid #e2e8f0',
            borderRadius:'10px', boxShadow:'0 8px 32px rgba(0,0,0,.12)',
            zIndex:'9999', maxHeight:'260px', overflowY:'auto'
        });
        input.parentElement.style.position = 'relative';

        function open()  { dropEl.style.display = 'block'; }
        function close() { dropEl.style.display = 'none'; dropEl.innerHTML = ''; }

        function render(q) {
            dropEl.innerHTML = '';
            if (!q) { close(); return; }
            var res = data.filter(function(m){
                return norm(m.id).includes(q) || norm(m.nom).includes(q)
                    || norm(m.description).includes(q) || norm(m.dosage).includes(q);
            }).slice(0, 8);

            if (!res.length) {
                var d = document.createElement('div');
                d.textContent = 'Aucun résultat pour "' + input.value + '"';
                Object.assign(d.style,{padding:'14px',textAlign:'center',color:'#94a3b8',fontSize:'13px'});
                dropEl.appendChild(d); open(); return;
            }
            res.forEach(function(m, i) {
                var item = document.createElement('div');
                Object.assign(item.style,{
                    display:'flex', alignItems:'center', gap:'8px', padding:'10px 14px',
                    cursor:'pointer', borderBottom: i < res.length-1 ? '1px solid #f1f5f9':'none'
                });
                item.onmouseover = function(){ item.style.background='#eff4ff'; };
                item.onmouseout  = function(){ item.style.background=''; };

                var parts = [
                    '<span style="font-size:11px;color:#94a3b8;flex-shrink:0">#'+m.id+'</span>',
                    '<span style="font-weight:600;color:#0f172a;flex:1;font-size:13px">'+esc(m.nom)+'</span>'
                ];
                if (m.dosage) parts.push('<span style="font-size:11px;color:#475569;background:#f1f5f9;padding:2px 8px;border-radius:100px">'+esc(m.dosage)+'</span>');
                if (m.forme)  parts.push('<span style="font-size:11px;color:#1a56db;background:#eff4ff;padding:2px 8px;border-radius:100px">'+esc(m.forme)+'</span>');
                item.innerHTML = parts.join('');

                item.addEventListener('mousedown', function(e){
                    e.preventDefault(); onSelect(m); close();
                });
                dropEl.appendChild(item);
            });
            open();
        }

        input.addEventListener('input',   function(){ render(norm(input.value.trim())); });
        input.addEventListener('focus',   function(){ if (input.value.trim()) render(norm(input.value.trim())); });
        input.addEventListener('blur',    function(){ setTimeout(close, 160); });
        input.addEventListener('keydown', function(e){ if (e.key==='Escape') close(); });
    }

    function esc(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    /* ══════════════════════════════════════
       CARTES MÉDICAMENT
    ══════════════════════════════════════ */
    function createCard(med, posologie, duree, quantite) {
        var idx   = container.querySelectorAll('.ligne-card').length;
        var label = esc(med.nom) + (med.dosage ? ' <small>('+esc(med.dosage)+')</small>' : '');

        var card = document.createElement('div');
        card.className   = 'ligne-card';
        card.dataset.index = idx;
        card.innerHTML =
            '<div class="ligne-card-header">' +
                '<div class="ligne-med-badge">' +
                    '<span class="ligne-med-icon">💊</span>' +
                    '<span class="ligne-med-nom">' + label + '</span>' +
                '</div>' +
                '<button type="button" class="btn-remove-ligne" title="Retirer">✕</button>' +
            '</div>' +
            '<input type="hidden" name="lignes['+idx+'][medicament_id]" class="med-hidden-id" value="'+med.id+'">' +
            '<div class="ligne-card-fields">' +
                '<div class="form-group">' +
                    '<label>Posologie <span class="required">*</span></label>' +
                    '<input type="text" name="lignes['+idx+'][posologie]" placeholder="Ex : 1 comprimé matin et soir" value="'+(posologie||'')+'" maxlength="200">' +
                    '<span class="field-hint">Comment et quand prendre le médicament</span>' +
                '</div>' +
                '<div class="form-group">' +
                    '<label>Durée</label>' +
                    '<input type="text" name="lignes['+idx+'][duree]" placeholder="Ex : 7 jours" value="'+(duree||'')+'" maxlength="50">' +
                '</div>' +
                '<div class="form-group ligne-qte">' +
                    '<label>Qté</label>' +
                    '<input type="number" name="lignes['+idx+'][quantite]" min="1" max="999" value="'+(quantite||1)+'">' +
                '</div>' +
            '</div>';

        var posEl = card.querySelector('input[name*="posologie"]');
        var durEl = card.querySelector('input[name*="duree"]');
        var qteEl = card.querySelector('input[name*="quantite"]');

        attachLive(posEl, 'posologie');
        attachLive(durEl, 'duree');
        attachLive(qteEl, 'quantite');

        qteEl.addEventListener('keypress', function(e){
            if (!/\d/.test(String.fromCharCode(e.charCode))) e.preventDefault();
        });

        qteEl.addEventListener('input', function() { updateCardWarnings(card); });
        durEl.addEventListener('input', function() { updateCardWarnings(card); });

        card.querySelector('.btn-remove-ligne').addEventListener('click', function(){
            card.remove(); reindex(); updateEmpty();
        });

        container.appendChild(card);
        updateEmpty();
        setTimeout(function(){
            posEl.focus();
            updateCardWarnings(card);
        }, 50);
        return card;
    }

    function reindex() {
        container.querySelectorAll('.ligne-card').forEach(function(card, i){
            card.dataset.index = i;
            card.querySelectorAll('[name]').forEach(function(el){
                el.name = el.name.replace(/lignes\[\d+\]/, 'lignes['+i+']');
            });
        });
    }

    function updateEmpty() {
        if (emptyMsg) emptyMsg.style.display = container.querySelectorAll('.ligne-card').length ? 'none' : 'block';
    }

    /* ── Init barre de recherche avec détection de doublon ── */
    buildDropdown(addInput, addDrop, function(med){
        var medId    = parseInt(med.id, 10);
        var existing = getExistingCard(medId);
        if (existing) {
            showDuplicateDialog(med, existing, function(action) {
                if (action === 'add') createCard(med, '', '', 1);
            });
        } else {
            createCard(med, '', '', 1);
        }
        addInput.value = '';
    });

    /* ── Init cartes existantes (edit / erreur POST) ── */
    container.querySelectorAll('.ligne-card').forEach(function(card){
        var btn = card.querySelector('.btn-remove-ligne');
        if (btn) btn.addEventListener('click', function(){ card.remove(); reindex(); updateEmpty(); });

        var posEl = card.querySelector('input[name*="posologie"]');
        var durEl = card.querySelector('input[name*="duree"]');
        var qteEl = card.querySelector('input[name*="quantite"]');
        if (posEl) attachLive(posEl, 'posologie');
        if (durEl) {
            attachLive(durEl, 'duree');
            durEl.addEventListener('input', function() { updateCardWarnings(card); });
        }
        if (qteEl) {
            attachLive(qteEl, 'quantite');
            qteEl.addEventListener('input', function() { updateCardWarnings(card); });
            qteEl.addEventListener('keypress', function(e){
                if (!/\d/.test(String.fromCharCode(e.charCode))) e.preventDefault();
            });
        }
        updateCardWarnings(card);
    });
    updateEmpty();

    /* ══════════════════════════════════════
       SOUMISSION
    ══════════════════════════════════════ */
    document.getElementById('ordonnance-form').addEventListener('submit', function(e) {
        clearAll();
        var ok = true;

        if (nomEl  && !validate(nomEl,  'patient_nom'))     ok = false;
        if (ageEl  && !validate(ageEl,  'patient_age'))     ok = false;
        if (dateEl && !validate(dateEl, 'date_ordonnance')) ok = false;

        var cards = container.querySelectorAll('.ligne-card');
        if (!cards.length) {
            ok = false;
            showBanner('Ajoutez au moins un médicament à l\'ordonnance.');
        } else {
            /* Détection de doublons au moment de la soumission */
            var submittedIds = [];
            var hasDuplicate = false;
            cards.forEach(function(card) {
                var h = card.querySelector('.med-hidden-id');
                if (h) {
                    var id = parseInt(h.value, 10);
                    if (submittedIds.indexOf(id) !== -1) hasDuplicate = true;
                    else submittedIds.push(id);
                }
            });
            if (hasDuplicate) {
                ok = false;
                showBanner('L\'ordonnance contient des médicaments en doublon. Supprimez les doublons ou utilisez la fusion de lignes.');
            }

            cards.forEach(function(card) {
                var posEl = card.querySelector('input[name*="posologie"]');
                var qteEl = card.querySelector('input[name*="quantite"]');
                var durEl = card.querySelector('input[name*="duree"]');
                if (posEl && !validate(posEl, 'posologie')) ok = false;
                if (durEl && !validate(durEl, 'duree'))     ok = false;
                if (qteEl && !validate(qteEl, 'quantite'))  ok = false;
            });
        }

        if (!ok) {
            e.preventDefault();
            var firstErr = document.querySelector('.field-error:not([style*="none"])');
            if (firstErr) firstErr.scrollIntoView({ behavior:'smooth', block:'center' });
            else showBanner('Veuillez corriger les erreurs avant de continuer.');
        }
    });

})();
