(function () {

    var btn     = document.getElementById('btn-resume-patient');
    var section = document.getElementById('resume-section');
    if (!btn || !section) return;

    btn.addEventListener('click', function () {
        if (!window.ORDONNANCE_DATA) return;

        /* Légère animation pour un meilleur ressenti */
        section.style.display = 'block';
        section.innerHTML =
            '<div class="resume-loading">' +
                '<span class="resume-spinner"></span> Génération du résumé…' +
            '</div>';
        btn.disabled = true;

        setTimeout(function () {
            var html = buildResume(
                window.ORDONNANCE_DATA.patient,
                window.ORDONNANCE_DATA.lignes
            );
            section.innerHTML = html;
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            btn.disabled = false;
        }, 500);
    });

    /* ════════════════════════════════════════
       GÉNÉRATION DU RÉSUMÉ
    ════════════════════════════════════════ */
    function buildResume(patient, lignes) {

        var age   = patient.age  ? parseInt(patient.age,  10) : null;
        var sexe  = patient.sexe || '';
        var isPediatric = age !== null && age < 12;
        var isSenior    = age !== null && age >= 65;

        /* ── En-tête ── */
        var intro = 'Bonjour <strong>' + esc(patient.nom) + '</strong>';
        if (age !== null) intro += ' (' + age + ' ans)';
        intro += ',<br>voici le résumé simple de votre ordonnance :';

        /* ── Médicaments ── */
        var medsHtml = '';
        lignes.forEach(function (ligne, i) {
            var titre = '<strong>' + esc(ligne.nom) + '</strong>';
            if (ligne.dosage) titre += ' <span class="resume-pill dosage">' + esc(ligne.dosage) + '</span>';
            if (ligne.forme)  titre += ' <span class="resume-pill forme">'  + esc(ligne.forme)  + '</span>';

            var infos = '';
            infos += resumeLi('📋', 'Comment prendre', esc(ligne.posologie));
            if (ligne.duree && ligne.duree.trim())
                infos += resumeLi('⏱', 'Durée', esc(ligne.duree));
            if (ligne.quantite && ligne.quantite > 0)
                infos += resumeLi('📦', 'Quantité', ligne.quantite + ' unité' + (ligne.quantite > 1 ? 's' : ''));

            medsHtml +=
                '<div class="resume-med-item">' +
                    '<div class="resume-med-title">' +
                        '<span class="resume-med-num">' + (i + 1) + '</span>' +
                        titre +
                    '</div>' +
                    '<ul class="resume-med-info">' + infos + '</ul>' +
                '</div>';
        });

        /* ── Conseils ── */
        var tips = [
            'Respectez les horaires et les doses prescrites par votre médecin.',
            'Ne sautez pas de prises, même si vous vous sentez mieux.',
            'Conservez vos médicaments à l\'abri de la chaleur et de l\'humidité.',
            'En cas d\'effet indésirable, contactez votre médecin ou pharmacien.'
        ];
        if (isPediatric)
            tips.push('⚠ Enfant : vérifiez bien la dose adaptée au poids de l\'enfant avec le médecin.');
        if (isSenior)
            tips.push('⚠ Signalez tout nouveau symptôme rapidement à votre médecin.');
        if (lignes.length > 2)
            tips.push('Vous avez plusieurs médicaments : respectez bien l\'ordre et les horaires pour éviter les interactions.');

        var tipsHtml = tips.map(function (t) {
            return '<li>' + t + '</li>';
        }).join('');

        /* ── Assemblage ── */
        return (
            '<div class="resume-card">' +
                '<div class="resume-card-header">' +
                    '<div class="resume-card-title">' +
                        '<span>📄</span>' +
                        '<h3>Résumé pour le patient</h3>' +
                    '</div>' +
                '</div>' +
                '<div class="resume-card-body">' +
                    '<p class="resume-intro">' + intro + '</p>' +
                    '<div class="resume-meds">' + medsHtml + '</div>' +
                    '<div class="resume-tips">' +
                        '<p class="resume-tips-title">💡 Conseils importants</p>' +
                        '<ul>' + tipsHtml + '</ul>' +
                    '</div>' +
                '</div>' +
                '<div class="resume-card-footer">' +
                    '<p class="resume-disclaimer">' +
                        'Ce résumé est fourni à titre informatif. En cas de doute, consultez votre pharmacien.' +
                    '</p>' +
                    '<button type="button" onclick="window.print()" class="resume-print-btn">🖨 Imprimer</button>' +
                '</div>' +
            '</div>'
        );
    }

    function resumeLi(icon, label, value) {
        return '<li>' + icon + ' <strong>' + label + ' :</strong> ' + value + '</li>';
    }

    function esc(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

})();
