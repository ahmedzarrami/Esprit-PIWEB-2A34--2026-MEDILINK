/**
 * suppRDV.js — Suppression d'un rendez-vous avec appel API
 */

async function deleteRDV(id) {
    if (!confirm("Voulez-vous vraiment supprimer ce rendez-vous ?")) return;

    try {
        const response = await fetch('/ProjetWeb/api.php?action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        });

        const result = await response.json();
        
        if (result.success) {
            alert("✅ Rendez-vous supprimé avec succès !");
            if (typeof window.refreshRDVList === "function") {
                window.refreshRDVList();
            }
        } else {
            alert("❌ Erreur : " + (result.message || "Impossible de supprimer"));
        }

    } catch (error) {
        console.error('Erreur:', error);
        alert("❌ Erreur de communication avec le serveur");
    }
}