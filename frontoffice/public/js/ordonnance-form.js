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
        /* ajuster si label présent */
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
       RÈGLES DE VALIDATION
    ══════════════════════════════════════ */
    var RULES = {
        patient_nom: function(el) {
            var v = el.value.trim();
            if (!v)          return 'Le nom du patient est obligatoire.';
            if (v.length < 2)return 'Minimum 2 caractères.';
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
            if (!v)           return 'La posologie est obligatoire.';
            if (v.length < 3) return 'Minimum 3 caractères (ex : 1 cp matin).';
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
            if (!v || v === '0') return 'La quantité doit être au moins 1.';
            if (!/^\d+$/.test(v)) return 'Nombre entier requis.';
            if (parseInt(v, 10) < 1)  return 'Minimum 1.';
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

    /* ── Attache la validation en temps réel ── */
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
    if (ageEl)  attachLive(ageEl,  'patient_age');
    if (dateEl) attachLive(dateEl, 'date_ordonnance', 'change');

    /* Bloquer saisie non-alphabétique dans le nom */
    if (nomEl) {
        nomEl.addEventListener('keypress', function(e) {
            var ch = String.fromCharCode(e.charCode);
            if (!/[\p{L}\s'\-\.]/u.test(ch)) e.preventDefault();
        });
    }

    /* Bloquer saisie non-numérique dans l'âge */
    if (ageEl) {
        ageEl.addEventListener('keypress', function(e) {
            if (!/\d/.test(String.fromCharCode(e.charCode))) e.preventDefault();
        });
    }

    /* ══════════════════════════════════════
       AUTOCOMPLETE
    ══════════════════════════════════════ */
    function norm(s) {
        return String(s).toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');
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

        /* Bloquer non-numérique dans quantité */
        qteEl.addEventListener('keypress', function(e){
            if (!/\d/.test(String.fromCharCode(e.charCode))) e.preventDefault();
        });

        card.querySelector('.btn-remove-ligne').addEventListener('click', function(){
            card.remove(); reindex(); updateEmpty();
        });

        container.appendChild(card);
        updateEmpty();
        setTimeout(function(){ posEl.focus(); }, 50);
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

    /* ── Init barre de recherche ── */
    buildDropdown(addInput, addDrop, function(med){
        createCard(med, '', '', 1);
        addInput.value = '';
    });

    /* ── Init cartes existantes (edit/erreur POST) ── */
    container.querySelectorAll('.ligne-card').forEach(function(card){
        var btn = card.querySelector('.btn-remove-ligne');
        if (btn) btn.addEventListener('click', function(){ card.remove(); reindex(); updateEmpty(); });

        var posEl = card.querySelector('input[name*="posologie"]');
        var durEl = card.querySelector('input[name*="duree"]');
        var qteEl = card.querySelector('input[name*="quantite"]');
        if (posEl) attachLive(posEl, 'posologie');
        if (durEl) attachLive(durEl, 'duree');
        if (qteEl) {
            attachLive(qteEl, 'quantite');
            qteEl.addEventListener('keypress', function(e){
                if (!/\d/.test(String.fromCharCode(e.charCode))) e.preventDefault();
            });
        }
    });
    updateEmpty();

    /* ══════════════════════════════════════
       SOUMISSION
    ══════════════════════════════════════ */
    document.getElementById('ordonnance-form').addEventListener('submit', function(e) {
        clearAll();
        var ok = true;

        if (nomEl  && !validate(nomEl,  'patient_nom'))        ok = false;
        if (ageEl  && !validate(ageEl,  'patient_age'))        ok = false;
        if (dateEl && !validate(dateEl, 'date_ordonnance'))    ok = false;

        var cards = container.querySelectorAll('.ligne-card');
        if (!cards.length) {
            ok = false;
            showBanner('Ajoutez au moins un médicament à l\'ordonnance.');
        } else {
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
