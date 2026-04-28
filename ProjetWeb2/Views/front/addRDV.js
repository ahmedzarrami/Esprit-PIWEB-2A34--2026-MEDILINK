/**
 * addRDV.js — Gestion complète des rendez-vous côté patient
 *
 * Contient exactement la logique du script de homePatient.php :
 *   - Chargement des médecins depuis l'API
 *   - Sélection médecin + créneaux horaires
 *   - Validation date (lun–sam, pas de date passée)
 *   - Validation heure (8h–12h30 ou 14h–18h)
 *   - Ajout, modification, suppression de rendez-vous
 *   - Chargement de la liste des RDV du patient
 *
 * Prérequis : PATIENT_ID doit être défini globalement dans la page PHP avant ce script.
 */

// ─────────────────────────────────────────
// LIMITES DE DATE (min = aujourd'hui, max = +30 jours)
// ─────────────────────────────────────────
function setDateLimits() {
    const dateInput = document.getElementById('date');
    if (!dateInput) return;
    const today = new Date();
    const minDate = today.toISOString().split('T')[0];
    const maxDate = new Date(today);
    maxDate.setDate(maxDate.getDate() + 30);
    dateInput.setAttribute('min', minDate);
    dateInput.setAttribute('max', maxDate.toISOString().split('T')[0]);
}

// ─────────────────────────────────────────
// VALIDATION DATE
// Règles : lundi–samedi uniquement, pas de date passée
// ─────────────────────────────────────────
function validateDate(dateStr) {
    if (!dateStr) return { ok: false, message: "Veuillez choisir une date." };

    const [year, month, day] = dateStr.split("-").map(Number);
    const date = new Date(year, month - 1, day);

    if (isNaN(date.getTime())) return { ok: false, message: "Date invalide." };

    if (date.getDay() === 0) {
        return { ok: false, message: "Le cabinet est fermé le dimanche. Veuillez choisir du lundi au samedi." };
    }

    const today = new Date();
    today.setHours(0, 0, 0, 0);
    if (date < today) {
        return { ok: false, message: "Impossible de réserver une date passée." };
    }

    return { ok: true };
}



// ─────────────────────────────────────────
// VALIDATION HEURE
// Plages autorisées : 08:00–12:30 et 14:00–18:00
// ─────────────────────────────────────────
function validateHeure(heureStr) {
    if (!heureStr) return { ok: false, message: "Veuillez choisir une heure." };

    const [h, m] = heureStr.split(":").map(Number);
    const totalMin = h * 60 + m;

    const DEBUT_MATIN = 8  * 60;
    const FIN_MATIN   = 12 * 60 + 30;
    const DEBUT_AM    = 14 * 60;
    const FIN_AM      = 18 * 60;

    const dansMatin = totalMin >= DEBUT_MATIN && totalMin < FIN_MATIN;
    const dansAM    = totalMin >= DEBUT_AM    && totalMin < FIN_AM;

    if (!dansMatin && !dansAM) {
        if (totalMin >= FIN_MATIN && totalMin < DEBUT_AM)
            return { ok: false, message: "Le cabinet est fermé de 12h30 à 14h00 (pause méridienne)." };
        if (totalMin < DEBUT_MATIN)
            return { ok: false, message: "Le cabinet ouvre à 8h00. Choisissez entre 8h00–12h30 ou 14h00–18h00." };
        if (totalMin >= FIN_AM)
            return { ok: false, message: "Le cabinet ferme à 18h00. Choisissez entre 8h00–12h30 ou 14h00–18h00." };
        return { ok: false, message: "Heure invalide. Consultations : 8h00–12h30 et 14h00–18h00." };
    }

    return { ok: true };
}

// ─────────────────────────────────────────
// ALERTES
// ─────────────────────────────────────────
function showAlert(msg, type, duration = 3000) {
    const alert = document.getElementById('formAlert');
    if (!alert) return;
    alert.textContent = msg;
    alert.className = 'form-alert visible alert-' + type;

    if (duration > 0) {
        setTimeout(() => alert.classList.remove('visible'), duration);
    }
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

// ─────────────────────────────────────────
// CHARGEMENT DES MÉDECINS
// ─────────────────────────────────────────
const AVATAR_COLORS = ['av-blue', 'av-teal', 'av-coral'];

function loadDoctors() {
    fetch('../../api.php?action=medecins')
        .then(r => r.json())
        .then(data => {
            if (data.success && data.data) {
                const doctorsList = document.getElementById('doctorsList');
                doctorsList.innerHTML = data.data.map((doc, idx) => {
                    const initials   = (doc.nom.substring(0, 1) + doc.specialite.substring(0, 1)).toUpperCase();
                    const colorClass = AVATAR_COLORS[idx % AVATAR_COLORS.length];
                    return `
                        <div class="doc-card">
                            <div class="doc-avatar ${colorClass}">${initials}</div>
                            <div class="doc-name">${doc.nom}</div>
                            <div class="doc-spec">${doc.specialite}</div>
                            <div class="doc-meta">
                                <div class="doc-meta-item"><strong>4.9</strong>Note</div>
                                <div class="doc-meta-item"><strong>10 ans</strong>Exp.</div>
                            </div>
                            <button type="button" class="btn-select-doc"
                                onclick="selectMedecinCard(${doc.id}, '${doc.nom} - ${doc.specialite}')">
                                Sélectionner
                            </button>
                        </div>`;
                }).join('');
            }
        })
        .catch(e => {
            console.error('Erreur chargement médecins:', e);
            document.getElementById('doctorsList').innerHTML =
                '<div class="empty-state"><strong>Erreur</strong><p>Impossible de charger les médecins</p></div>';
        });
}

// ─────────────────────────────────────────
// SÉLECTION D'UN MÉDECIN
// ─────────────────────────────────────────
function selectMedecinCard(medecinId, medecinLabel) {
    document.getElementById('medecin').value = medecinId;

    const date = document.getElementById('date').value;
    if (date) {
        document.getElementById('timePickerGrid').innerHTML =
            '<div class="empty-slots">Chargement des créneaux...</div>';
        loadTimeSlots(medecinId, date);
    } else {
        document.getElementById('timePickerGrid').innerHTML =
            '<div class="empty-slots">📅 Sélectionnez maintenant une date</div>';
    }

    document.getElementById('rdvForm').scrollIntoView({ behavior: 'smooth' });
    showAlert('✓ Médecin sélectionné: ' + medecinLabel, 'success', 0);
}

// ─────────────────────────────────────────
// CRÉNEAUX HORAIRES
// ─────────────────────────────────────────
function loadTimeSlots(medecinId, date) {
    if (!medecinId || !date) {
        document.getElementById('timePickerGrid').innerHTML =
            '<div class="empty-slots">📅 Sélectionnez le médecin et la date</div>';
        return;
    }

    fetch(`../../api.php?action=get_medecin_availability&medecin_id=${medecinId}&date=${date}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                displayTimeSlots(data.slots);
            } else {
                document.getElementById('timePickerGrid').innerHTML =
                    '<div class="empty-slots">⚠️ Erreur de chargement des créneaux</div>';
            }
        })
        .catch(e => {
            console.error('Erreur:', e);
            document.getElementById('timePickerGrid').innerHTML =
                '<div class="empty-slots">⚠️ Erreur réseau</div>';
        });
}

function displayTimeSlots(slots) {
    const grid = document.getElementById('timePickerGrid');
    if (!slots || slots.length === 0) {
        grid.innerHTML = '<div class="empty-slots">⚠️ Aucun créneau disponible</div>';
        return;
    }
    grid.innerHTML = slots.map(slot => {
        const className = slot.occupied ? 'slot occupied' : 'slot available';
        const disabled  = slot.occupied ? 'disabled' : '';
        let label = slot.slot;
        let title = slot.slot;

        if (slot.type === 'lunch') {
            title = 'Pause déjeuner';
            label = slot.slot + '<br><small style="font-size:10px;opacity:0.8;">Pause</small>';
        } else if (slot.type === 'booked') {
            title = 'Déjà réservé';
            label = slot.slot + '<br><small style="font-size:10px;opacity:0.8;">Réservé</small>';
        }

        return `<button type="button" ${disabled} class="${className}" data-time="${slot.slot}"
            title="${title}" onclick="selectTimeSlot('${slot.slot}', ${slot.occupied})">
            ${label}
        </button>`;
    }).join('');
}

function selectTimeSlot(time, occupied) {
    if (occupied) return;

    document.querySelectorAll('.slot.selected').forEach(el => el.classList.remove('selected'));
    document.querySelectorAll('[data-time]').forEach(btn => {
        if (btn.getAttribute('data-time') === time && !btn.disabled) {
            btn.classList.add('selected');
        }
    });

    document.getElementById('heure').value = time;
    document.getElementById('heureError').textContent = '';
    clearFieldError('heure', 'heureError');
}

// ─────────────────────────────────────────
// LISTE DES RDV DU PATIENT
// ─────────────────────────────────────────
function loadPatientRDV() {
    fetch('../../api.php?action=list_rendezvous_patient&patient_id=' + PATIENT_ID)
        .then(r => r.json())
        .then(data => {
            const rdvList = document.getElementById('rdvList');
            if (!data.success || data.rendezvous.length === 0) {
                rdvList.innerHTML = `
                    <div class="empty-state">
                        <div style="font-size:28px;margin-bottom:10px">📭</div>
                        <strong>Aucun rendez-vous</strong>
                        <p style="color:var(--gray-400);margin-top:6px">
                            Vous n'avez pas encore de rendez-vous. Réservez-en un ci-dessus!
                        </p>
                    </div>`;
            } else {
                rdvList.innerHTML = data.rendezvous.map(rdv => `
                    <div class="rdv-item" id="rdv-${rdv.id}">
                        <div class="rdv-icon">📅</div>
                        <div class="rdv-info">
                            <div class="rdv-doc">${rdv.medecin_nom}</div>
                            <div class="rdv-spec">${rdv.specialite}</div>
                            <div class="rdv-time">${rdv.date_rdv} à ${rdv.heure_rdv}</div>
                        </div>
                        <div class="rdv-status">
                            <span class="rdv-status-dot"></span>
                            ${rdv.statut}
                        </div>
                        <div class="rdv-actions">
                            <button class="btn-action btn-edit"
                                onclick="editRendezvous(${rdv.id}, '${rdv.medecin_nom}', '${rdv.date_rdv}', '${rdv.heure_rdv}')">
                                ✏️ Modifier
                            </button>
                            <button class="btn-action btn-delete"
                                onclick="deleteRendezvous(${rdv.id})">
                                🗑️ Supprimer
                            </button>
                        </div>
                    </div>`).join('');
            }
        })
        .catch(e => {
            console.error('Erreur:', e);
            document.getElementById('rdvList').innerHTML =
                '<div class="empty-state"><strong>Erreur</strong><p>Impossible de charger les rendez-vous</p></div>';
        });
}

// ─────────────────────────────────────────
// SUPPRESSION D'UN RDV
// ─────────────────────────────────────────
function deleteRendezvous(rdvId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous?')) return;

    fetch('../../api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=delete_rendezvous&id=' + rdvId
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAlert('✓ Rendez-vous supprimé avec succès!', 'success', 3000);
            const item = document.getElementById('rdv-' + rdvId);
            if (item) {
                item.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => { item.remove(); loadPatientRDV(); }, 300);
            }
        } else {
            showAlert('❌ ' + (data.message || 'Erreur lors de la suppression'), 'error', 3000);
        }
    })
    .catch(e => {
        console.error('Erreur:', e);
        showAlert('❌ Erreur réseau', 'error', 3000);
    });
}

// ─────────────────────────────────────────
// MODIFICATION D'UN RDV
// ─────────────────────────────────────────
function editRendezvous(rdvId, medecinNom, date, heure) {
    document.getElementById('medecin').value = '';
    document.getElementById('date').value    = date;
    document.getElementById('heure').value   = heure;

    showAlert('✏️ Mode édition : Modifiez la date/heure et confirmez. RDV #' + rdvId, 'info', 0);

    window.editingRdvId = rdvId;

    const submitBtn = document.querySelector('.btn-confirm');
    submitBtn.dataset.originalText = submitBtn.textContent;
    submitBtn.textContent = '💾 Mettre à jour le rendez-vous';

    document.getElementById('rdvForm').scrollIntoView({ behavior: 'smooth' });
}

// ─────────────────────────────────────────
// SOUMISSION DU FORMULAIRE (ajout + modif)
// ─────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

    setDateLimits();

    // Listener changement de date avec validation
    document.getElementById('date').addEventListener('change', function () {
        clearFieldError('date', 'dateError');

        const dateResult = validateDate(this.value);
        if (!dateResult.ok) {
            showFieldError('date', 'dateError', dateResult.message);
            document.getElementById('timePickerGrid').innerHTML =
                '<div class="empty-slots">⚠️ ' + dateResult.message + '</div>';
            return;
        }

        const medecinId = document.getElementById('medecin').value;
        if (medecinId) {
            document.getElementById('timePickerGrid').innerHTML =
                '<div class="empty-slots">Chargement des créneaux...</div>';
            loadTimeSlots(medecinId, this.value);
        } else {
            document.getElementById('timePickerGrid').innerHTML =
                '<div class="empty-slots">👨‍⚕️ Sélectionnez d\'abord un médecin ci-dessus</div>';
        }
    });

    // Soumission
    document.getElementById('rdvForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const medecin   = document.getElementById('medecin').value;
        const date      = document.getElementById('date').value;
        const heure     = document.getElementById('heure').value;
        const submitBtn = document.querySelector('.btn-confirm');

        // 1. Médecin requis
        if (!medecin) {
            showAlert('Veuillez sélectionner un médecin avant de confirmer.', 'error', 3000);
            return;
        }

        // 2. Validation date
        const dateResult = validateDate(date);
        if (!dateResult.ok) {
            showFieldError('date', 'dateError', dateResult.message);
            showAlert(dateResult.message, 'error', 3000);
            return;
        }

        // 3. Validation heure
        const heureResult = validateHeure(heure);
        if (!heureResult.ok) {
            showFieldError('heure', 'heureError', heureResult.message);
            showAlert(heureResult.message, 'error', 3000);
            return;
        }

        // ── Mode édition ──
        if (window.editingRdvId) {
            fetch('../../api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=update_rendezvous&id=' + window.editingRdvId +
                      '&date_rdv=' + date + '&heure_rdv=' + heure + '&statut=confirmé'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showAlert('✓ Rendez-vous mis à jour avec succès!', 'success', 3000);
                    document.getElementById('rdvForm').reset();
                    submitBtn.textContent = submitBtn.dataset.originalText;
                    window.editingRdvId   = null;
                    document.getElementById('timePickerGrid').innerHTML =
                        '<div class="empty-slots">📅 Sélectionnez le médecin et la date</div>';
                    setTimeout(loadPatientRDV, 800);
                } else {
                    showAlert('❌ ' + (data.message || 'Erreur lors de la mise à jour'), 'error', 3000);
                }
            })
            .catch(e => { console.error(e); showAlert('❌ Erreur réseau', 'error', 3000); });

        // ── Mode création ──
        } else {
            fetch('../../api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=add_rendezvous&medecin_id=' + medecin +
                      '&patient_id=' + PATIENT_ID +
                      '&date_rdv=' + date + '&heure_rdv=' + heure + '&statut=confirmé'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showAlert('✓ Rendez-vous réservé avec succès!', 'success', 3000);
                    document.getElementById('rdvForm').reset();
                    document.getElementById('timePickerGrid').innerHTML =
                        '<div class="empty-slots">📅 Sélectionnez le médecin et la date</div>';
                    setTimeout(loadPatientRDV, 800);
                } else {
                    showAlert('❌ ' + (data.message || 'Erreur lors de la réservation'), 'error', 3000);
                }
            })
            .catch(e => { console.error(e); showAlert('❌ Erreur réseau', 'error', 3000); });
        }
    });

    // Animation suppression
    const styleTag = document.createElement('style');
    styleTag.textContent = `
        @keyframes fadeOut {
            from { opacity: 1; transform: translateX(0); }
            to   { opacity: 0; transform: translateX(-20px); }
        }`;
    document.head.appendChild(styleTag);

    // Chargement initial
    loadDoctors();
    loadPatientRDV();
});