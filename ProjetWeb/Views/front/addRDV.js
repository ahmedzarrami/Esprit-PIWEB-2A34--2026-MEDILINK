/**
 * addRDV.js — Sélection médecin + ajout RDV avec contrôle de saisie complet
 *
 * Règles métier :
 *  - Jours autorisés  : lundi à samedi (dimanche interdit)
 *  - Horaires matin   : 08:00 – 12:30
 *  - Pause méridienne : 12:30 – 14:00 (interdit)
 *  - Horaires après-midi : 14:00 – 18:00
 *  - Doublons interdits : même date + même heure
 */

/* ─────────────────────────────────────────
   SÉLECTION MÉDECIN
───────────────────────────────────────── */
function selectMedecin(nom, spec, cardId) {
    document.getElementById("medecin").value = nom;
    document.getElementById("badgeNom").textContent = nom + " — " + spec;
    document.getElementById("medecinBadge").classList.add("visible");

    document.querySelectorAll(".doc-card").forEach(c => c.classList.remove("selected-doctor"));
    const card = document.getElementById(cardId);
    if (card) card.classList.add("selected-doctor");

    // Effacer les alertes si on change de médecin
    clearAlert();
}

/* ─────────────────────────────────────────
   FONCTIONS UTILITAIRES D'AFFICHAGE
───────────────────────────────────────── */
function showAlert(message, type) {
    // type = 'error' | 'warning' | 'success'
    const box  = document.getElementById("formAlert");
    const msg  = document.getElementById("formAlertMsg");
    const icon = document.getElementById("formAlertIcon");
    box.className = "form-alert visible alert-" + type;
    
    if (type === "error") {
        icon.textContent = "⛔";
    } else if (type === "warning") {
        icon.textContent = "⚠️";
    } else if (type === "success") {
        icon.textContent = "✅";
    }
    
    msg.textContent  = message;
    box.scrollIntoView({ behavior: "smooth", block: "nearest" });
}

function clearAlert() {
    const box = document.getElementById("formAlert");
    if (box) box.className = "form-alert";
}

function showFieldError(fieldId, errorId, message) {
    const field = document.getElementById(fieldId);
    const err   = document.getElementById(errorId);
    if (field) field.classList.add("input-error");
    if (err)   { err.textContent = message; err.classList.add("visible"); }
}

function clearFieldError(fieldId, errorId) {
    const field = document.getElementById(fieldId);
    const err   = document.getElementById(errorId);
    if (field) field.classList.remove("input-error");
    if (err)   { err.textContent = ""; err.classList.remove("visible"); }
}

function clearAllErrors() {
    clearAlert();
    clearFieldError("date",  "dateError");
    clearFieldError("heure", "heureError");
}

/* ─────────────────────────────────────────
   VALIDATION DATE
───────────────────────────────────────── */
function validateDate(dateStr) {
    if (!dateStr) {
        return { ok: false, message: "Veuillez choisir une date." };
    }

    // Utiliser les parties de la chaîne pour éviter les problèmes de fuseau horaire
    const [year, month, day] = dateStr.split("-").map(Number);
    const date = new Date(year, month - 1, day);

    // Vérifier que la date est valide
    if (isNaN(date.getTime())) {
        return { ok: false, message: "Date invalide." };
    }

    // getDay() : 0 = dimanche, 6 = samedi
    const jourSemaine = date.getDay();
    if (jourSemaine === 0) {
        return { ok: false, message: "Le cabinet est fermé le dimanche. Veuillez choisir du lundi au samedi." };
    }

    // Interdire les dates passées
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    if (date < today) {
        return { ok: false, message: "Impossible de réserver une date passée." };
    }

    return { ok: true };
}

/* ─────────────────────────────────────────
   VALIDATION HEURE
───────────────────────────────────────── */
function validateHeure(heureStr) {
    if (!heureStr) {
        return { ok: false, message: "Veuillez choisir une heure." };
    }

    const [h, m] = heureStr.split(":").map(Number);
    // Convertir en minutes depuis minuit pour faciliter les comparaisons
    const totalMin = h * 60 + m;

    const DEBUT_MATIN   = 8  * 60;       // 08:00
    const FIN_MATIN     = 12 * 60 + 30;  // 12:30
    const DEBUT_AM      = 14 * 60;       // 14:00
    const FIN_AM        = 18 * 60;       // 18:00

    // Vérifier que l'heure est dans les plages autorisées
    const dansMatin = totalMin >= DEBUT_MATIN && totalMin < FIN_MATIN;
    const dansAM    = totalMin >= DEBUT_AM    && totalMin < FIN_AM;

    if (!dansMatin && !dansAM) {
        if (totalMin >= FIN_MATIN && totalMin < DEBUT_AM) {
            return {
                ok: false,
                message: "Le cabinet est fermé de 12h30 à 14h00 (pause méridienne)."
            };
        }
        if (totalMin < DEBUT_MATIN) {
            return {
                ok: false,
                message: "Le cabinet ouvre à 8h00. Veuillez choisir une heure entre 8h00–12h30 ou 14h00–18h00."
            };
        }
        if (totalMin >= FIN_AM) {
            return {
                ok: false,
                message: "Le cabinet ferme à 18h00. Veuillez choisir une heure entre 8h00–12h30 ou 14h00–18h00."
            };
        }
        return {
            ok: false,
            message: "Heure invalide. Consultations : 8h00–12h30 et 14h00–18h00."
        };
    }

    return { ok: true };
}

/* ─────────────────────────────────────────
   VÉRIFICATION DOUBLON - APPEL API
───────────────────────────────────────── */
async function checkDoublon(date, heure, medecinId) {
    try {
        const response = await fetch('/ProjetWeb/api.php?action=list');
        const result = await response.json();
        
        if (result.success && result.data) {
            return result.data.some(r => 
                r.date_rdv === date && 
                r.heure_rdv === heure && 
                r.medecin_id == medecinId
            );
        }
        return false;
    } catch (error) {
        console.error('Erreur vérification doublon:', error);
        return false;
    }
}

/* ─────────────────────────────────────────
   SOUMISSION DU FORMULAIRE
───────────────────────────────────────── */
document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("rdvForm");
    if (!form) return;

    // Effacer les erreurs au changement de champ
    document.getElementById("date").addEventListener("change", function () {
        clearFieldError("date", "dateError");
        clearAlert();
        // Validation en temps réel
        const result = validateDate(this.value);
        if (!result.ok) showFieldError("date", "dateError", result.message);
    });

    document.getElementById("heure").addEventListener("change", function () {
        clearFieldError("heure", "heureError");
        clearAlert();
        // Validation en temps réel
        const result = validateHeure(this.value);
        if (!result.ok) showFieldError("heure", "heureError", result.message);
    });

    form.addEventListener("submit", async function (e) {
        e.preventDefault();
        clearAllErrors();

        const medecinText = document.getElementById("medecin").value.trim();
        const date    = document.getElementById("date").value;
        const heure   = document.getElementById("heure").value;

        console.log("📋 Tentative d'ajout - Médecin:", medecinText, "Date:", date, "Heure:", heure);

        let hasError = false;

        // 1. Vérifier médecin
        if (!medecinText) {
            showAlert("Veuillez sélectionner un médecin avant de confirmer.", "error");
            return;
        }

        // 2. Valider la date
        const dateResult = validateDate(date);
        if (!dateResult.ok) {
            showFieldError("date", "dateError", dateResult.message);
            hasError = true;
        }

        // 3. Valider l'heure
        const heureResult = validateHeure(heure);
        if (!heureResult.ok) {
            showFieldError("heure", "heureError", heureResult.message);
            hasError = true;
        }

        if (hasError) {
            showAlert("Veuillez corriger les erreurs ci-dessous.", "error");
            return;
        }

        // 4. Obtenir l'ID du médecin depuis la liste
        try {
            console.log("🔍 Récupération de la liste des médecins...");
            const medecinResponse = await fetch('/ProjetWeb/api.php?action=medecins');
            console.log("✓ Réponse médecins:", medecinResponse.status);
            
            const medecinResult = await medecinResponse.json();
            console.log("✓ Données médecins:", medecinResult);
            
            let medecinId = null;
            if (medecinResult.success && medecinResult.data) {
                const medecin = medecinResult.data.find(m => m.nom.toLowerCase().includes(medecinText.toLowerCase()));
                if (medecin) medecinId = medecin.id;
            }

            if (!medecinId) {
                console.error("❌ Médecin non trouvé:", medecinText);
                showAlert("Médecin non trouvé. Veuillez sélectionner un médecin valide.", "error");
                return;
            }

            console.log("✓ Médecin trouvé avec ID:", medecinId);

            // 5. Vérifier doublon (même date ET même heure ET même médecin)
            if (await checkDoublon(date, heure, medecinId)) {
                showAlert(
                    "Vous avez déjà un rendez-vous le " + formatDate(date) + " à " + heure + ". Veuillez choisir un autre créneau.",
                    "warning"
                );
                showFieldError("date",  "dateError",  "Créneau déjà réservé");
                showFieldError("heure", "heureError", "Créneau déjà réservé");
                return;
            }

            // ✅ Tout est valide — appel API pour ajouter
            console.log("📤 Envoi du rendez-vous...");
            const response = await fetch('/ProjetWeb/api.php?action=add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    medecin_id: medecinId,
                    date_rdv: date,
                    heure_rdv: heure,
                    statut: 'confirmé'
                })
            });

            console.log("✓ Réponse API:", response.status);
            const result = await response.json();
            console.log("✓ Résultat API:", result);
            
            if (result.success) {
                showAlert("✅ Rendez-vous ajouté avec succès !", "success");
                
                // Réinitialiser le formulaire
                form.reset();
                document.getElementById("medecin").value = "";
                document.getElementById("medecinBadge").classList.remove("visible");
                document.querySelectorAll(".doc-card").forEach(c => c.classList.remove("selected-doctor"));
                clearAllErrors();

                // Rafraîchir la liste
                if (typeof window.refreshRDVList === "function") {
                    window.refreshRDVList();
                }

                // Scroll vers la liste
                const rdvList = document.getElementById("rdvList");
                if (rdvList) rdvList.scrollIntoView({ behavior: "smooth", block: "start" });
            } else {
                console.error("❌ Erreur API:", result.message);
                showAlert("❌ " + (result.message || "Erreur lors de l'ajout"), "error");
            }

        } catch (error) {
            console.error('❌ Erreur:', error);
            showAlert("❌ Erreur de communication avec le serveur: " + error.message, "error");
        }
    });
});

/* ─────────────────────────────────────────
   HELPER : formater une date en français
───────────────────────────────────────── */
function formatDate(dateStr) {
    if (!dateStr) return "";
    const [y, m, d] = dateStr.split("-");
    const jours = ["dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi"];
    const mois  = ["janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre"];
    const date  = new Date(Number(y), Number(m) - 1, Number(d));
    return jours[date.getDay()] + " " + Number(d) + " " + mois[Number(m) - 1] + " " + y;
}