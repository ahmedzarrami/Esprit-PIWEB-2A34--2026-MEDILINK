<?php
$basePath = dirname(dirname(dirname(__DIR__)));
require_once $basePath . '/config.php';
require_once $basePath . '/controllers/fichePatientC.php';

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
            --blue:        #2563eb;
            --blue-dark:   #1d4ed8;
            --blue-light:  #eff6ff;
            --blue-mid:    #60a5fa;
            --green:       #10b981;
            --green-light: #ecfdf5;
            --navy:        #0f172a;
            --navy2:       #1e293b;
            --red:         #ef4444;
            --red-light:   #fef2f2;
            --orange-light:#fffbeb;
            --gray-50:     #f8fafc;
            --gray-100:    #f1f5f9;
            --gray-200:    #e2e8f0;
            --gray-400:    #94a3b8;
            --gray-600:    #475569;
            --gray-900:    #0f172a;
            --radius:      16px;
            --radius-lg:   24px;
            --radius-xl:   32px;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--gray-50); color: var(--gray-900); font-size: 14px; line-height: 1.6; overflow-x: hidden; }

        /* ── NAVBAR ── */
        .navbar-medilink {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 0 40px;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 30px rgba(0,0,0,0.03);
        }
        .nav-logo { display: flex; align-items: center; text-decoration: none; font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
        .nav-logo span { background: linear-gradient(135deg, var(--blue), #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

        .nav-links { display: flex; gap: 8px; }
        .nav-links a {
            padding: 8px 20px;
            border-radius: 12px;
            color: var(--gray-600);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all .3s ease;
        }
        .nav-links a:hover, .nav-links a.active {
            background: var(--blue-light);
            color: var(--blue);
        }

        .btn-home {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--navy), #1e293b);
            color: #fff;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all .3s ease;
            box-shadow: 0 4px 15px rgba(15,23,42,0.2);
        }
        .btn-home:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(15,23,42,0.3); }

        /* ── HERO ── */
        .hero {
            background: linear-gradient(135deg, var(--navy) 0%, #1e293b 100%);
            padding: 100px 40px 120px;
            position: relative;
            overflow: hidden;
        }
        .hero::before { content:''; position:absolute; top:-50%; right:-10%; width:60%; height:200%; background:radial-gradient(circle,rgba(37,99,235,0.3) 0%,rgba(15,23,42,0) 60%); transform:rotate(-15deg); pointer-events:none; }
        .hero::after { content:''; position:absolute; bottom:-20%; left:-10%; width:50%; height:150%; background:radial-gradient(circle,rgba(16,185,129,0.2) 0%,rgba(15,23,42,0) 60%); transform:rotate(15deg); pointer-events:none; }
        .hero-inner { max-width:960px; margin:0 auto; position:relative; z-index:1; }
        .hero h1 { font-size:48px; font-weight:800; color:#fff; line-height:1.15; margin-bottom:16px; letter-spacing:-1px; }
        .hero p { color:rgba(255,255,255,.75); font-size:18px; max-width:500px; line-height:1.6; font-weight:400; }

        /* ── MAIN ── */
        .main-content { max-width:960px; margin:-40px auto 80px; padding:0 40px; position:relative; z-index:10; }

        .section-heading {
            font-size: 18px; font-weight: 800; color: var(--gray-900);
            margin-bottom: 24px; display: flex; align-items: center; gap: 12px; letter-spacing: -0.5px;
        }
        .section-heading::before {
            content: ''; display: inline-block; width: 4px; height: 20px;
            background: linear-gradient(180deg, var(--blue), #8b5cf6); border-radius: 4px;
        }

        /* ── STATS GRID ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.9);
            border-radius: var(--radius-lg);
            padding: 28px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.04);
            position: relative;
            overflow: hidden;
        }
        .stat-card::before { content:''; position:absolute; left:0; top:0; bottom:0; width:4px; background:linear-gradient(180deg, var(--blue), #8b5cf6); }
        .stat-label {
            font-size: 13px;
            color: var(--gray-600);
            margin-bottom: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .stat-value {
            font-size: 36px;
            font-weight: 800;
            color: var(--gray-900);
            letter-spacing: -1px;
        }

        /* ── BUTTON ── */
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 24px;
            background: linear-gradient(135deg, var(--blue), #8b5cf6);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all .3s ease;
            margin-bottom: 24px;
            box-shadow: 0 8px 25px rgba(37,99,235,0.3);
        }
        .btn-print:hover { transform: translateY(-2px); box-shadow: 0 12px 35px rgba(37,99,235,0.4); }

        /* ── REPORT CARD ── */
        .report-card {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.9);
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.04);
        }
        .report-header {
            background: linear-gradient(135deg, var(--blue), #8b5cf6);
            color: #fff;
            padding: 24px 32px;
        }
        .report-header h2 {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .report-content {
            padding: 32px;
        }

        /* ── FICHE ITEM ── */
        .fiche-item {
            border-bottom: 1px dashed var(--gray-200);
            padding: 24px 0;
            transition: all .3s ease;
        }
        .fiche-item:last-child {
            border-bottom: none;
        }
        .fiche-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }
        .fiche-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }
        .info-box {
            background: var(--gray-50);
            border: 1px solid rgba(0,0,0,0.05);
            padding: 16px;
            border-radius: 12px;
        }
        .info-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 8px;
        }
        .info-value {
            color: var(--gray-900);
            font-size: 15px;
            font-weight: 600;
        }

        /* ── INFO BLOCKS ── */
        .info-block {
            margin-top: 16px;
            padding: 16px;
            border-radius: 12px;
            border-left: 4px solid;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }
        .info-block.allergies {
            background: var(--orange-light);
            border-left-color: #f59e0b;
            color: #92400e;
        }
        .info-block.antecedents {
            background: var(--blue-light);
            border-left-color: var(--blue);
            color: var(--blue-dark);
        }
        .info-block.notes {
            background: var(--gray-50);
            border-left-color: var(--gray-400);
            color: var(--gray-900);
        }
        .info-block strong {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            font-size: 14px;
        }
        .info-block p {
            font-size: 14px;
            line-height: 1.6;
        }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: rgba(255,255,255,0.5);
            border-radius: var(--radius-lg);
            border: 1px dashed var(--gray-300);
        }
        .empty-icon {
            font-size: 48px;
            margin-bottom: 16px;
            color: var(--gray-300);
        }
        .empty-text {
            color: var(--gray-500);
            font-size: 16px;
            font-weight: 600;
        }

        /* ── BOUTON PDF ── */
        .btn-pdf {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: var(--red-light);
            color: var(--red);
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all .3s ease;
        }
        .btn-pdf:hover { background: var(--red); color:#fff; transform: translateY(-1px); box-shadow: 0 6px 15px rgba(239,68,68,0.2); }
        .btn-pdf:disabled { opacity:.6; cursor:not-allowed; transform:none; box-shadow:none; }
        .fiche-actions {
            display: flex;
            justify-content: flex-end;
            padding: 16px 0 24px;
            margin-top: 0;
            border-bottom: 2px solid rgba(0,0,0,0.05);
        }
        .fiche-actions:last-child {
            border-bottom: none;
        }

        /* ── PRINT ── */
        @media print {
            body { background: white; }
            .navbar-medilink, .btn-print, .btn-pdf, .fiche-actions { display: none; }
            .stat-card, .report-card { box-shadow:none; border:1px solid #ccc; backdrop-filter:none; }
        }

        /* ── RESPONSIVE ── */
        @media (max-width:768px) {
            .navbar-medilink { padding: 0 20px; }
            .main-content { padding: 20px; }
            .hero { padding: 60px 20px 80px; }
            .hero h1 { font-size: 36px; }
            .stats-grid { grid-template-columns: 1fr; }
            .fiche-grid { grid-template-columns: 1fr; }
            .main { margin-left: 0; }
        }
        
        .main {
            margin-left: 280px;
            min-height: 100vh;
        }
        .topbar {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 16px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }
        .topbar-title { font-size: 18px; font-weight: 800; color: var(--gray-900); letter-spacing: -0.3px; }
        .topbar-subtitle { font-size: 13px; color: var(--gray-500); margin-top: 2px; font-weight: 500; }
    </style>
</head>
<body>
    
    <?php include dirname(dirname(dirname(__DIR__))) . '/views/back/layouts/sidebar.php'; ?>

    <div class="main">
        <div class="topbar">
            <div>
                <div class="topbar-title">Rapport des Fiches Patients</div>
                <div class="topbar-subtitle">Vue d'ensemble complète des dossiers médicaux</div>
            </div>
        </div>

        <div class="main-content" style="max-width: 1200px; margin: 40px auto; padding: 0 40px;">
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
    </div> <!-- /main -->
</body>
</html>