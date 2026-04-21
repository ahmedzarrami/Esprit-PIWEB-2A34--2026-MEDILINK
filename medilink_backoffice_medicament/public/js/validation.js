document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('medicament-form');
  if (!form) return;

  const rules = {
    nom: value => value.trim().length >= 2 ? '' : 'Le nom doit contenir au moins 2 caractères.',
    dosage: value => value.trim() !== '' ? '' : 'Le dosage est obligatoire.',
    forme: value => value.trim() !== '' ? '' : 'La forme est obligatoire.',
    fabricant: value => value.trim() !== '' ? '' : 'Le fabricant est obligatoire.',
    description: value => value.trim().length >= 10 ? '' : 'La description doit contenir au moins 10 caractères.',
    prix: value => {
      const normalized = value.replace(',', '.').trim();
      const number = Number(normalized);
      return normalized !== '' && Number.isFinite(number) && number > 0 ? '' : 'Le prix doit être un nombre positif.';
    },
    stock: value => {
      const trimmed = value.trim();
      return /^\d+$/.test(trimmed) ? '' : 'Le stock doit être un entier positif ou nul.';
    }
  };

  const showError = (field, message) => {
    const errorBlock = field.parentElement.querySelector('.error');
    field.classList.toggle('invalid', message !== '');
    if (errorBlock) {
      errorBlock.textContent = message;
    }
  };

  const validateField = field => {
    const ruleName = field.dataset.rule;
    if (!ruleName || !rules[ruleName]) return true;
    const message = rules[ruleName](field.value);
    showError(field, message);
    return message === '';
  };

  form.querySelectorAll('[data-rule]').forEach(field => {
    field.addEventListener('input', () => validateField(field));
    field.addEventListener('blur', () => validateField(field));
  });

  form.addEventListener('submit', event => {
    let isValid = true;
    form.querySelectorAll('[data-rule]').forEach(field => {
      if (!validateField(field)) {
        isValid = false;
      }
    });

    if (!isValid) {
      event.preventDefault();
    }
  });
});
