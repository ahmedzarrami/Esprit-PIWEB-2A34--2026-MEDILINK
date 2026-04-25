document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('medicament-form');
  if (!form) return;

  const normalizeSpaces = value => value.replace(/\s+/g, ' ').trim();
  const normalizePrice = value => value.replace(',', '.').trim();

  const rules = {
    nom: value => {
      const cleaned = normalizeSpaces(value);
      if (cleaned === '') return 'Le nom du médicament est obligatoire.';
      if (cleaned.length < 3) return 'Le nom doit contenir au moins 3 caractères.';
      if (cleaned.length > 100) return 'Le nom ne doit pas dépasser 100 caractères.';
      return /^[\p{L}0-9 .,'()\-\/]+$/u.test(cleaned)
        ? ''
        : 'Le nom contient des caractères non autorisés.';
    },
    dosage: value => {
      const cleaned = normalizeSpaces(value);
      if (cleaned === '') return 'Le dosage est obligatoire.';
      if (cleaned.length < 2) return 'Le dosage doit contenir au moins 2 caractères.';
      if (cleaned.length > 50) return 'Le dosage ne doit pas dépasser 50 caractères.';
      return /\d/.test(cleaned) ? '' : 'Le dosage doit contenir au moins un chiffre.';
    },
    forme: value => {
      const cleaned = normalizeSpaces(value);
      if (cleaned === '') return 'La forme est obligatoire.';
      if (cleaned.length < 2) return 'La forme doit contenir au moins 2 caractères.';
      if (cleaned.length > 50) return 'La forme ne doit pas dépasser 50 caractères.';
      return '';
    },
    fabricant: value => {
      const cleaned = normalizeSpaces(value);
      if (cleaned === '') return 'Le fabricant est obligatoire.';
      if (cleaned.length < 2) return 'Le fabricant doit contenir au moins 2 caractères.';
      if (cleaned.length > 100) return 'Le fabricant ne doit pas dépasser 100 caractères.';
      return '';
    },
    description: value => {
      const cleaned = normalizeSpaces(value);
      if (cleaned === '') return 'La description du médicament est obligatoire.';
      if (cleaned.length < 15) return 'La description doit contenir au moins 15 caractères.';
      if (cleaned.length > 500) return 'La description ne doit pas dépasser 500 caractères.';
      return '';
    },
    prix: value => {
      const normalized = normalizePrice(value);
      if (normalized === '') return 'Le prix est obligatoire.';
      if (!/^\d+(\.\d{1,2})?$/.test(normalized)) return 'Le prix doit contenir au maximum 2 décimales.';
      const number = Number(normalized);
      return Number.isFinite(number) && number > 0 ? '' : 'Le prix doit être un nombre positif.';
    }
  };

  const showError = (field, message) => {
    const errorBlock = field.parentElement.querySelector('.error');
    field.classList.toggle('invalid', message !== '');
    field.setAttribute('aria-invalid', message !== '' ? 'true' : 'false');
    if (errorBlock) errorBlock.textContent = message;
  };

  const validateField = field => {
    const ruleName = field.dataset.rule;
    if (!ruleName || !rules[ruleName]) return true;
    const message = rules[ruleName](field.value);
    showError(field, message);
    return message === '';
  };

  const fields = Array.from(form.querySelectorAll('[data-rule]'));
  fields.forEach(field => {
    field.addEventListener('input', () => validateField(field));
    field.addEventListener('blur', () => validateField(field));
  });

  form.addEventListener('submit', event => {
    let firstInvalidField = null;
    let isValid = true;

    fields.forEach(field => {
      if (!validateField(field)) {
        isValid = false;
        if (!firstInvalidField) firstInvalidField = field;
      }
    });

    if (!isValid) {
      event.preventDefault();
      if (firstInvalidField) firstInvalidField.focus();
    }
  });
});
