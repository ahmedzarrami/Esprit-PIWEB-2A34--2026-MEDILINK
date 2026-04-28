/**
 * modifRDV.js — Modification d'un rendez-vous avec appel API
 * Réutilise validateDate() et validateHeure() de addRDV.js
 */

async function editRDV(id) {
    try {
        // Récupérer les données du RDV
        const response = await fetch(`/ProjetWeb/api.php?action=get&id=${id}`);
        const result = await response.json();

        if (!result.success || !result.data) {
            alert("Rendez-vous introuvable.");
            return;
        }

        const rdv = result.data;

        // ── Nouvelle date ──
        let newDate = prompt(
            "Nouvelle date (YYYY-MM-DD) :\n" +
            "⚠️ Lundi au samedi uniquement · Pas de date passée",
            rdv.date_rdv
        );
        if (newDate === null) return; // annulé
        newDate = newDate.trim();

        const dateResult = validateDate(newDate);
        if (!dateResult.ok) {
            alert("❌ Date invalide :\n" + dateResult.message);
            return;
        }

        // ── Nouvelle heure ──
        let newHeure = prompt(
            "Nouvelle heure (HH:MM) :\n" +
            "⚠️ 8h00–12h30 ou 14h00–18h00",
            rdv.heure_rdv
        );
        if (newHeure === null) return; // annulé
        newHeure = newHeure.trim();

        const heureResult = validateHeure(newHeure);
        if (!heureResult.ok) {
            alert("❌ Heure invalide :\n" + heureResult.message);
            return;
        }

        // ── Vérifier doublon (exclure le RDV actuel) ──
        const rdvList = await fetch('/ProjetWeb/api.php?action=list');
        const listResult = await rdvList.json();
        
        if (listResult.success && listResult.data) {
            const doublon = listResult.data.some(r => 
                r.id != id && 
                r.date_rdv === newDate && 
                r.heure_rdv === newHeure &&
                r.medecin_id == rdv.medecin_id
            );
            
            if (doublon) {
                alert("⚠️ Vous avez déjà un rendez-vous à ce créneau.\nVeuillez choisir une autre date ou heure.");
                return;
            }
        }

        // ✅ Mise à jour via API
        const updateResponse = await fetch('/ProjetWeb/api.php?action=update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id,
                medecin_id: rdv.medecin_id,
                date_rdv: newDate,
                heure_rdv: newHeure,
                statut: rdv.statut
            })
        });

        const updateResult = await updateResponse.json();
        
        if (updateResult.success) {
            alert("✅ Rendez-vous modifié avec succès !");
            if (typeof window.refreshRDVList === "function") {
                window.refreshRDVList();
            }
        } else {
            alert("❌ Erreur : " + (updateResult.message || "Impossible de modifier"));
        }

    } catch (error) {
        console.error('Erreur:', error);
        alert("❌ Erreur de communication avec le serveur");
    }
}