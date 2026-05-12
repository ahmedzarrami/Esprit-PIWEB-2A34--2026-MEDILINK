/**
 * listRDV.js — Affichage de la liste des rendez-vous depuis la base de données
 * Expose window.refreshRDVList() pour les autres modules.
 */

document.addEventListener("DOMContentLoaded", function () {

    const container = document.getElementById("rdvList");
    if (!container) return;

    async function renderRDVs() {
        try {
            const response = await fetch('/ProjetWeb/api.php?action=list');
            const result = await response.json();
            
            let rdvs = [];
            if (result.success && result.data) {
                rdvs = result.data;
            }

            // Mettre à jour le compteur stats
            const statCount = document.getElementById("statCount");
            if (statCount) statCount.textContent = rdvs.length + " RDV";

            if (!rdvs.length) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div style="font-size:36px">📋</div>
                        <strong>Aucun rendez-vous</strong>
                        Sélectionnez un médecin et choisissez un créneau ci-dessus.
                    </div>`;
                return;
            }

            container.innerHTML = rdvs.map(r => `
                <div class="rdv-item">
                    <div class="rdv-icon">📅</div>
                    <div class="rdv-info">
                        <div class="rdv-doc">${r.medecin_nom} — ${r.specialite}</div>
                        <div class="rdv-time">${formatDateFR(r.date_rdv)} &nbsp;·&nbsp; ${r.heure_rdv}</div>
                    </div>
                    <div class="rdv-status">
                        <span class="rdv-status-dot"></span>
                        ${r.statut.charAt(0).toUpperCase() + r.statut.slice(1)}
                    </div>
                    <div class="rdv-actions">
                        <button class="btn-edit" onclick="editRDV(${r.id})">Modifier</button>
                        <button class="btn-del"  onclick="deleteRDV(${r.id})">Supprimer</button>
                    </div>
                </div>
            `).join("");
        } catch (error) {
            console.error('Erreur lors du chargement des RDV:', error);
            container.innerHTML = `
                <div class="empty-state">
                    <div style="font-size:36px">⚠️</div>
                    <strong>Erreur de chargement</strong>
                    Impossible de récupérer les rendez-vous.
                </div>`;
        }
    }

    function formatDateFR(dateStr) {
        if (!dateStr) return dateStr;
        const [y, m, d] = dateStr.split("-");
        const jours = ["Dim","Lun","Mar","Mer","Jeu","Ven","Sam"];
        const mois  = ["jan","fév","mar","avr","mai","juin","juil","août","sep","oct","nov","déc"];
        const date  = new Date(Number(y), Number(m) - 1, Number(d));
        return jours[date.getDay()] + " " + Number(d) + " " + mois[Number(m) - 1] + ". " + y;
    }

    renderRDVs();
    window.refreshRDVList = renderRDVs;
});