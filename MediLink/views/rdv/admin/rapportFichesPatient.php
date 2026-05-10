<?php
$basePath = dirname(__DIR__) . '/..';
require_once $basePath . '/config.php';
require_once $basePath . '/Controller/fichePatientC.php';

$fichePatientC = new FichePatientC();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink — Rapport Fiches Patients</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --blue:        #1a56db;
            --blue-dark:   #1a46c4;
            --blue-light:  #eff4ff;
            --blue-mid:    #6694f8;
            --green:       #0da271;
            --green-light: #ecfdf5;
            --navy:        #0f1b2d;
            --navy2:       #1e2f45;
            --red:         #dc2626;
            --red-light:   #fee2e2;
            --orange-light:#fef3c7;
            --gray-50:     #f8fafc;
            --gray-100:    #f1f5f9;
            --gray-200:    #e2e8f0;
            --gray-400:    #94a3b8;
            --gray-600:    #475569;
            --gray-900:    #0f172a;
            --radius:      12px;
            --radius-lg:   18px;
            --radius-xl:   24px;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--gray-50); color: var(--gray-900); font-size: 14px; line-height: 1.6; }

        /* ── NAVBAR ── */
        .navbar-medilink {
            background: #fff;
            border-bottom: 1px solid var(--gray-200);
            padding: 0 40px;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .nav-logo { display: flex; align-items: center; text-decoration: none; font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
        .nav-logo span { background: linear-gradient(90deg, #1a56db 0%, #0da271 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

        .nav-links { display: flex; gap: 4px; }
        .nav-links a {
            padding: 6px 16px;
            border-radius: 8px;
            color: var(--gray-600);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: .15s;
        }
        .nav-links a:hover, .nav-links a.active {
            background: var(--blue-light);
            color: var(--blue);
        }

        .btn-home {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            background: var(--navy);
            color: #fff;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: .15s;
        }
        .btn-home:hover { background: var(--navy2); }

        /* ── HERO ── */
        .hero {
            background: linear-gradient(135deg, #1a46c4 0%, #2563eb 55%, #3b7ff7 100%);
            padding: 72px 40px 88px;
            position: relative;
            overflow: hidden;
        }
        .hero::before { content:''; position:absolute; top:-80px; right:-80px; width:350px; height:350px; background:rgba(255,255,255,.06); border-radius:50%; }
        .hero::after { content:''; position:absolute; bottom:-100px; left:42%; width:220px; height:220px; background:rgba(255,255,255,.04); border-radius:50%; }
        .hero-inner { max-width:920px; margin:0 auto; position:relative; z-index:1; }
        .hero h1 { font-size:40px; font-weight:600; color:#fff; line-height:1.2; margin-bottom:14px; }
        .hero p { color:rgba(255,255,255,.75); font-size:15px; max-width:460px; line-height:1.75; }

        /* ── MAIN ── */
        .main-content { max-width:920px; margin:0 auto; padding:44px 40px 60px; }

        .section-heading {
            font-size: 15px; font-weight: 600; color: var(--gray-900);
            margin-bottom: 20px; display: flex; align-items: center; gap: 10px;
        }
        .section-heading::before {
            content: ''; display: inline-block; width: 3px; height: 16px;
            background: var(--blue); border-radius: 2px;
        }

        /* ── STATS GRID ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 20px;
            border-left: 4px solid var(--blue);
        }
        .stat-label {
            font-size: 12px;
            color: var(--gray-600);
            margin-bottom: 10px;
        }
        .stat-value {
            font-size: 28px;
            font-weight: 600;
            color: var(--blue);
        }

        /* ── BUTTON ── */
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            background: var(--blue);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: .15s;
            margin-bottom: 22px;
        }
        .btn-print:hover { background: var(--blue-dark); }

        /* ── REPORT CARD ── */
        .report-card {
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-xl);
            overflow: hidden;
        }
        .report-header {
            background: var(--blue);
            color: #fff;
            padding: 20px 28px;
        }
        .report-header h2 {
            font-size: 16px;
            font-weight: 600;
        }
        .report-content {
            padding: 28px;
        }

        /* ── FICHE ITEM ── */
        .fiche-item {
            border-bottom: 1px solid var(--gray-200);
            padding: 20px 0;
        }
        .fiche-item:last-child {
            border-bottom: none;
        }
        .fiche-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--blue);
            margin-bottom: 14px;
        }
        .fiche-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 14px;
            margin-bottom: 14px;
        }
        .info-box {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            padding: 12px;
            border-radius: var(--radius);
        }
        .info-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 6px;
        }
        .info-value {
            color: var(--gray-900);
            font-size: 13px;
        }

        /* ── INFO BLOCKS ── */
        .info-block {
            margin-top: 14px;
            padding: 12px;
            border-radius: var(--radius);
            border-left: 4px solid;
        }
        .info-block.allergies {
            background: var(--orange-light);
            border-left-color: #fbbf24;
            color: #92400e;
        }
        .info-block.antecedents {
            background: var(--blue-light);
            border-left-color: var(--blue);
            color: var(--blue-dark);
        }
        .info-block.notes {
            background: var(--gray-100);
            border-left-color: var(--gray-600);
            color: var(--gray-900);
        }
        .info-block strong {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .info-block p {
            font-size: 13px;
            line-height: 1.5;
        }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        .empty-text {
            color: var(--gray-400);
            font-size: 14px;
        }

        /* ── BOUTON PDF ── */
        .btn-pdf {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            background: var(--red);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: .15s;
        }
        .btn-pdf:hover { background: #b91c1c; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(220,38,38,.3); }
        .btn-pdf:disabled { opacity:.6; cursor:not-allowed; transform:none; box-shadow:none; }
        .fiche-actions {
            display: flex;
            justify-content: flex-end;
            padding: 10px 0 18px;
            margin-top: 0;
            border-bottom: 2px solid var(--gray-200);
        }
        .fiche-actions:last-child {
            border-bottom: none;
        }

        /* ── PRINT ── */
        @media print {
            body { background: white; }
            .navbar-medilink, .btn-print, .btn-pdf, .fiche-actions { display: none; }
        }

        /* ── RESPONSIVE ── */
        @media (max-width:700px) {
            .navbar-medilink { padding: 0 16px; }
            .main-content { padding: 28px 16px 48px; }
            .hero { padding: 48px 20px 60px; }
            .hero h1 { font-size: 28px; }
            .stats-grid { grid-template-columns: 1fr; }
            .fiche-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- ── NAVBAR ── -->
    <nav class="navbar-medilink">
        <a href="admin.php" class="nav-logo">
            <span>MediLink</span>
        </a>
        <div class="nav-links">
            <a href="admin.php">Accueil</a>
            <a href="rapportFichesPatient.php" class="active">Rapport Fiches</a>
        </div>
        <a href="../../index.php" class="btn-home">
             Retour accueil
        </a>
    </nav>

    <!-- ── HERO ── -->
    <div class="hero">
        <div class="hero-inner">
            <h1>Rapport des Fiches Patients</h1>
            <p>Vue d'ensemble complète des dossiers médicaux et des diagnostics enregistrés.</p>
        </div>
    </div>

    <!-- ── MAIN CONTENT ── -->
    <div class="main-content">
        <?php
        try {
            $fiches = $fichePatientC->listFichePatient();
            $total_fiches = count($fiches);
            $patients_allergiques = 0;
            $groupes_sanguins = [];

            foreach ($fiches as $fiche) {
                if (!empty($fiche['allergies'])) {
                    $patients_allergiques++;
                }
                $groupe = $fiche['groupsanguin'] ?? 'Non spécifié';
                $groupes_sanguins[$groupe] = ($groupes_sanguins[$groupe] ?? 0) + 1;
            }
        ?>

            <!-- ── STATS ── -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">📋 Total Fiches Patients</div>
                    <div class="stat-value"><?php echo $total_fiches; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">⚠️ Patients Allergiques</div>
                    <div class="stat-value"><?php echo $patients_allergiques; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">🩸 Groupes Sanguins</div>
                    <div class="stat-value"><?php echo count($groupes_sanguins); ?></div>
                </div>
            </div>

            <!-- ── REPORT ── -->
            <div class="report-card">
                <div class="report-header">
                    <h2>Détail des Fiches Patients</h2>
                </div>
                <div class="report-content">
                    <?php if (empty($fiches)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📋</div>
                            <div class="empty-text">Aucune fiche patient enregistrée</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($fiches as $fiche): ?>
                            <div class="fiche-item" id="fiche-<?php echo $fiche['idfiche']; ?>">
                                <div class="fiche-title">
                                    Fiche #<?php echo htmlspecialchars($fiche['idfiche']); ?> &mdash;
                                    <?php
                                        $nom_patient = trim(($fiche['patient_prenom'] ?? '') . ' ' . ($fiche['patient_nom'] ?? ''));
                                        echo $nom_patient ? '&#128100; ' . htmlspecialchars($nom_patient) : '&#128100; Patient inconnu';
                                    ?>
                                    <span style="font-weight:400; color:var(--gray-600); font-size:13px;"> &middot; Dr. <?php echo htmlspecialchars($fiche['medecin_nom']); ?></span>
                                </div>

                                <div class="fiche-grid">
                                    <div class="info-box">
                                        <div class="info-label">👤 Patient</div>
                                        <div class="info-value"><?php $np = trim(($fiche['patient_prenom'] ?? '') . ' ' . ($fiche['patient_nom'] ?? '')); echo $np ? htmlspecialchars($np) : '<em style="color:var(--gray-400)">Non renseigné</em>'; ?></div>
                                    </div>
                                    <div class="info-box">
                                        <div class="info-label">📅 Date du RDV</div>
                                        <div class="info-value"><?php echo htmlspecialchars($fiche['date_rdv']); ?> à <?php echo htmlspecialchars($fiche['heure_rdv']); ?></div>
                                    </div>
                                    <div class="info-box">
                                        <div class="info-label">🩸 Groupe Sanguin</div>
                                        <div class="info-value"><?php echo htmlspecialchars($fiche['groupsanguin'] ?? 'Non spécifié'); ?></div>
                                    </div>
                                    <div class="info-box">
                                        <div class="info-label">⚕️ Spécialité</div>
                                        <div class="info-value"><?php echo htmlspecialchars($fiche['specialite']); ?></div>
                                    </div>
                                    <div class="info-box">
                                        <div class="info-label">📝 Créée le</div>
                                        <div class="info-value"><?php echo htmlspecialchars($fiche['date_creation']); ?></div>
                                    </div>
                                </div>

                                <?php if (!empty($fiche['allergies'])): ?>
                                    <div class="info-block allergies">
                                        <strong>⚠️ Allergies</strong>
                                        <p><?php echo htmlspecialchars($fiche['allergies']); ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($fiche['antecedents'])): ?>
                                    <div class="info-block antecedents">
                                        <strong>📚 Antécédents Médicaux</strong>
                                        <p><?php echo htmlspecialchars($fiche['antecedents']); ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($fiche['notesGenerales'])): ?>
                                    <div class="info-block notes">
                                        <strong>📝 Notes Générales</strong>
                                        <p><?php echo htmlspecialchars($fiche['notesGenerales']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div><!-- fin fiche-item -->
                            <div class="fiche-actions">
                                <button class="btn-pdf"
                                        onclick="exporterPDF(<?php echo $fiche['idfiche']; ?>, this)">
                                    &#128196; Exporter cette fiche en PDF
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        <?php } catch (Exception $e) {
            echo '<div style="background: var(--red-light); padding: 15px; border-radius: var(--radius-lg); color: var(--red); border: 1px solid rgba(220, 38, 38, 0.2);">❌ Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>
    <script>
    async function exporterPDF(ficheId, btn) {
        const { jsPDF } = window.jspdf;
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = "&#9203; Generation...";
        try {
            const el = document.getElementById("fiche-" + ficheId);
            if (!el) { alert("Fiche introuvable"); return; }
            const canvas = await html2canvas(el, {
                scale: 2, useCORS: true,
                backgroundColor: "#ffffff", logging: false
            });
            const imgData = canvas.toDataURL("image/png");
            const pdf     = new jsPDF({ orientation: "p", unit: "mm", format: "a4" });
            const pageW   = pdf.internal.pageSize.getWidth();
            const pageH   = pdf.internal.pageSize.getHeight();
            const margin  = 15;
            const imgW    = pageW - margin * 2;
            const imgH    = (canvas.height * imgW) / canvas.width;
            // En-tete bleu
            pdf.setFillColor(26, 86, 219);
            pdf.rect(0, 0, pageW, 22, "F");
            pdf.setTextColor(255, 255, 255);
            pdf.setFontSize(12); pdf.setFont("helvetica", "bold");
            pdf.text("MediLink  Fiche Patient #" + ficheId, margin, 14);
            pdf.setFontSize(8); pdf.setFont("helvetica", "normal");
            const now = new Date().toLocaleDateString("fr-FR", {
                day:"2-digit", month:"2-digit", year:"numeric",
                hour:"2-digit", minute:"2-digit"
            });
            pdf.text("Exporte le " + now, pageW - margin, 14, { align: "right" });
            // Contenu (multi-pages si fiche longue)
            const startY = 28;
            if (imgH + startY <= pageH - 15) {
                pdf.addImage(imgData, "PNG", margin, startY, imgW, imgH);
            } else {
                const ratio = canvas.width / imgW;
                let srcY = 0, posY = startY, heightLeft = imgH;
                while (heightLeft > 0.5) {
                    const sliceH  = Math.min(pageH - posY - 15, heightLeft);
                    const slicePx = Math.round(sliceH * ratio);
                    const sc = document.createElement("canvas");
                    sc.width = canvas.width; sc.height = slicePx;
                    const ctx = sc.getContext("2d");
                    ctx.fillStyle = "#fff"; ctx.fillRect(0,0,sc.width,sc.height);
                    ctx.drawImage(canvas, 0, srcY, canvas.width, slicePx, 0, 0, canvas.width, slicePx);
                    pdf.addImage(sc.toDataURL("image/png"), "PNG", margin, posY, imgW, sliceH);
                    heightLeft -= sliceH; srcY += slicePx;
                    if (heightLeft > 0.5) { pdf.addPage(); posY = 10; }
                }
            }
            // Pied de page
            const total = pdf.internal.getNumberOfPages();
            for (let i = 1; i <= total; i++) {
                pdf.setPage(i);
                pdf.setDrawColor(220,220,220);
                pdf.line(margin, pageH-13, pageW-margin, pageH-13);
                pdf.setFontSize(8); pdf.setTextColor(150,150,150);
                pdf.setFont("helvetica","normal");
                pdf.text("MediLink  Document confidentiel", margin, pageH-7);
                pdf.text("Page "+i+" / "+total, pageW-margin, pageH-7, {align:"right"});
            }
            pdf.save("fiche-patient-" + ficheId + ".pdf");
        } catch(err) {
            console.error("Erreur PDF:", err);
            alert("Erreur lors de la generation du PDF.");
        } finally {
            btn.disabled = false;
            btn.innerHTML = orig;
        }
    }
    </script>
</body>
</html>