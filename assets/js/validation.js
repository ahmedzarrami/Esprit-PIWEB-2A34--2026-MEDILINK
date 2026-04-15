/**
 * Validator — Classe de validation JavaScript (OOP)
 * Remplace toute validation HTML5 native
 * MediLink — Gestion des Utilisateurs
 */
class Validator {

    /**
     * Vérifie qu'un champ n'est pas vide
     */
    static isRequired(value) {
        return value !== null && value !== undefined && value.toString().trim() !== '';
    }

    /**
     * Vérifie la longueur minimale
     */
    static minLength(value, min) {
        return value.toString().trim().length >= min;
    }

    /**
     * Vérifie la longueur maximale
     */
    static maxLength(value, max) {
        return value.toString().trim().length <= max;
    }

    /**
     * Vérifie le format email
     */
    static isEmail(value) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(value.toString().trim());
    }

    /**
     * Vérifie le format téléphone — exactement 8 chiffres
     */
    static isPhone(value) {
        const digits = value.toString().trim().replace(/\D/g, '');
        return digits.length === 8;
    }

    /**
     * Vérifie si une valeur est une date valide (format AAAA-MM-JJ)
     */
    static isDate(value) {
        if (!value || value.toString().trim() === '') return false;
        const regex = /^\d{4}-\d{2}-\d{2}$/;
        if (!regex.test(value.toString().trim())) return false;
        const d = new Date(value);
        return !isNaN(d.getTime());
    }

    /**
     * Vérifie que la date est dans le passé (âge entre 0 et 120 ans)
     */
    static isValidBirthDate(value) {
        if (!Validator.isDate(value)) return false;
        const d = new Date(value);
        const age = (new Date() - d) / 31557600000;
        return age >= 0 && age <= 120;
    }

    /**
     * Vérifie qu'une valeur correspond à un pattern regex
     */
    static matchesPattern(value, regex) {
        return regex.test(value.toString().trim());
    }

    /**
     * Vérifie que le nom/prénom contient uniquement des caractères valides
     */
    static isValidName(value) {
        const regex = /^[a-zA-ZÀ-ÿ\s.\-']+$/;
        return regex.test(value.toString().trim());
    }

    /**
     * Vérifie le format du numéro d'ordre médical
     */
    static isValidOrdre(value) {
        const regex = /^TN-[A-Z]{2,5}-\d{4,6}$/;
        return regex.test(value.toString().trim());
    }

    /**
     * Calcule la force d'un mot de passe
     * Retourne : { score: 0-4, label: string, color: string, percent: string }
     */
    static passwordStrength(value) {
        const pw = value.toString();
        let score = 0;
        if (pw.length >= 8)         score++;
        if (/[A-Z]/.test(pw))       score++;
        if (/[0-9]/.test(pw))       score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;

        const levels = [
            { label: '',            color: '',        percent: '0%'   },
            { label: 'Très faible', color: '#dc2626', percent: '25%'  },
            { label: 'Faible',      color: '#d97706', percent: '50%'  },
            { label: 'Moyen',       color: '#2563eb', percent: '75%'  },
            { label: 'Fort',        color: '#059669', percent: '100%' }
        ];

        return {
            score: pw ? score : 0,
            ...levels[pw ? score : 0]
        };
    }

    /**
     * Vérifie que deux mots de passe correspondent
     */
    static passwordsMatch(password, confirmPassword) {
        return password === confirmPassword;
    }

    // ══════════════════════════════════════════
    // FILTRAGE DE SAISIE EN TEMPS RÉEL
    // ══════════════════════════════════════════

    /**
     * Filtre un champ pour n'autoriser que des lettres (pas de chiffres)
     * À utiliser : oninput="Validator.filterTextOnly(this)"
     */
    static filterTextOnly(input) {
        input.value = input.value.replace(/[0-9]/g, '');
    }

    /**
     * Filtre un champ pour n'autoriser que des chiffres
     * À utiliser : oninput="Validator.filterNumbersOnly(this)"
     */
    static filterNumbersOnly(input) {
        input.value = input.value.replace(/[^0-9]/g, '');
    }

    /**
     * Filtre un champ téléphone : uniquement chiffres, max 8 caractères
     * À utiliser : oninput="Validator.filterPhone(this)"
     */
    static filterPhone(input) {
        input.value = input.value.replace(/[^0-9]/g, '');
        if (input.value.length > 8) {
            input.value = input.value.substring(0, 8);
        }
    }

    // ══════════════════════════════════════════
    // AFFICHAGE DES ERREURS
    // ══════════════════════════════════════════

    /**
     * Affiche une erreur sur un champ
     */
    static showFieldError(inputId, errorId, message) {
        const inp = document.getElementById(inputId);
        const err = document.getElementById(errorId);
        if (inp) {
            inp.classList.add('err', 'error');
            inp.classList.remove('ok', 'success');
        }
        if (err) {
            const span = err.querySelector('span');
            if (span) span.textContent = message;
            err.classList.remove('hidden');
            err.style.display = 'flex';
        }
    }

    /**
     * Efface l'erreur d'un champ
     */
    static clearFieldError(inputId, errorId) {
        const inp = document.getElementById(inputId);
        const err = document.getElementById(errorId);
        if (inp) {
            inp.classList.remove('err', 'error');
            inp.classList.add('ok', 'success');
        }
        if (err) {
            err.classList.add('hidden');
            err.style.display = 'none';
        }
    }

    /**
     * Efface toutes les erreurs d'un formulaire
     */
    static clearAllErrors(fieldIds) {
        fieldIds.forEach(id => {
            const inp = document.getElementById(id);
            if (inp) {
                inp.classList.remove('err', 'error', 'ok', 'success');
            }
        });
    }
}

// ══════════════════════════════════════════════════════════════════
// DatePicker — Calendrier personnalisé (OOP)
// Remplace l'utilisation de <input type="date">
// ══════════════════════════════════════════════════════════════════
class DatePicker {
    constructor(inputElement) {
        this.input = inputElement;
        this.isOpen = false;
        this.currentMonth = new Date().getMonth();
        this.currentYear = new Date().getFullYear();
        this.selectedDate = null;

        // Lire la valeur existante
        if (this.input.value && Validator.isDate(this.input.value)) {
            const d = new Date(this.input.value);
            this.currentMonth = d.getMonth();
            this.currentYear = d.getFullYear();
            this.selectedDate = d;
        }

        this.wrapper = null;
        this.popup = null;
        this.init();
    }

    init() {
        // Wrapper autour de l'input
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'datepicker-wrapper';
        this.input.parentNode.insertBefore(this.wrapper, this.input);
        this.wrapper.appendChild(this.input);

        // Icône calendrier
        const icon = document.createElement('span');
        icon.className = 'datepicker-icon';
        icon.innerHTML = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>';
        this.wrapper.appendChild(icon);

        // Popup calendrier
        this.popup = document.createElement('div');
        this.popup.className = 'datepicker-popup';
        this.wrapper.appendChild(this.popup);

        // Input en lecture seule
        this.input.readOnly = true;
        this.input.style.cursor = 'pointer';

        // Événements
        this.input.addEventListener('click', (e) => { e.stopPropagation(); this.toggle(); });
        icon.addEventListener('click', (e) => { e.stopPropagation(); this.toggle(); });
        document.addEventListener('click', (e) => {
            if (this.isOpen && !this.wrapper.contains(e.target)) this.close();
        });

        this.render();
    }

    toggle() {
        this.isOpen ? this.close() : this.open();
    }

    open() {
        this.isOpen = true;
        this.popup.classList.add('open');
        this.render();
    }

    close() {
        this.isOpen = false;
        this.popup.classList.remove('open');
    }

    prevMonth() {
        this.currentMonth--;
        if (this.currentMonth < 0) { this.currentMonth = 11; this.currentYear--; }
        this.render();
    }

    nextMonth() {
        this.currentMonth++;
        if (this.currentMonth > 11) { this.currentMonth = 0; this.currentYear++; }
        this.render();
    }

    selectDate(year, month, day) {
        this.selectedDate = new Date(year, month, day);
        const m = String(month + 1).padStart(2, '0');
        const d = String(day).padStart(2, '0');
        this.input.value = `${year}-${m}-${d}`;
        // Déclencher les événements
        this.input.dispatchEvent(new Event('input', { bubbles: true }));
        this.input.dispatchEvent(new Event('change', { bubbles: true }));
        this.close();
    }

    render() {
        const mois = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
        const jours = ['Lu','Ma','Me','Je','Ve','Sa','Di'];
        const today = new Date();

        // Générer les options d'année (1920 → année courante)
        let yearOptions = '';
        for (let y = today.getFullYear(); y >= 1920; y--) {
            yearOptions += `<option value="${y}" ${y === this.currentYear ? 'selected' : ''}>${y}</option>`;
        }

        let html = `
        <div class="dp-header">
            <button type="button" class="dp-nav" data-action="prev">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <div class="dp-title">
                <span class="dp-month-label">${mois[this.currentMonth]}</span>
                <select class="dp-year-select">${yearOptions}</select>
            </div>
            <button type="button" class="dp-nav" data-action="next">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
        <div class="dp-days-header">${jours.map(j => `<span>${j}</span>`).join('')}</div>
        <div class="dp-days">`;

        const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
        const offset = firstDay === 0 ? 6 : firstDay - 1; // Lundi = 0
        const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();

        // Cases vides avant le 1er jour
        for (let i = 0; i < offset; i++) {
            html += '<span class="dp-empty"></span>';
        }

        for (let day = 1; day <= daysInMonth; day++) {
            let cls = 'dp-day';
            const cellDate = new Date(this.currentYear, this.currentMonth, day);

            // Pas de date future pour date de naissance
            if (cellDate > today) cls += ' dp-disabled';

            // Aujourd'hui
            if (day === today.getDate() && this.currentMonth === today.getMonth() && this.currentYear === today.getFullYear()) {
                cls += ' dp-today';
            }

            // Date sélectionnée
            if (this.selectedDate && day === this.selectedDate.getDate() && this.currentMonth === this.selectedDate.getMonth() && this.currentYear === this.selectedDate.getFullYear()) {
                cls += ' dp-selected';
            }

            html += `<span class="${cls}" data-day="${day}">${day}</span>`;
        }

        html += '</div>';

        // Bouton "Aujourd'hui"
        html += '<div class="dp-footer"><button type="button" class="dp-today-btn">Aujourd\'hui</button></div>';

        this.popup.innerHTML = html;

        // Event listeners via delegation
        this.popup.querySelector('[data-action="prev"]').addEventListener('click', (e) => { e.stopPropagation(); this.prevMonth(); });
        this.popup.querySelector('[data-action="next"]').addEventListener('click', (e) => { e.stopPropagation(); this.nextMonth(); });

        this.popup.querySelector('.dp-year-select').addEventListener('change', (e) => {
            e.stopPropagation();
            this.currentYear = parseInt(e.target.value);
            this.render();
        });

        this.popup.querySelectorAll('.dp-day:not(.dp-disabled)').forEach(d => {
            d.addEventListener('click', (e) => {
                e.stopPropagation();
                this.selectDate(this.currentYear, this.currentMonth, parseInt(d.dataset.day));
            });
        });

        const todayBtn = this.popup.querySelector('.dp-today-btn');
        if (todayBtn) {
            todayBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.currentMonth = today.getMonth();
                this.currentYear = today.getFullYear();
                this.selectDate(today.getFullYear(), today.getMonth(), today.getDate());
            });
        }
    }
}

// ══════════════════════════════════════════
// AUTO-INITIALISATION AU CHARGEMENT
// ══════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les datepickers
    document.querySelectorAll('.datepicker-input').forEach(input => {
        new DatePicker(input);
    });
});
