function prependElement(element, prepend) {
    element.parentNode.insertBefore(prepend, element);
}

function appendElement(element, prepend) {
    element.parentNode.insertBefore(prepend, element.nextSibling);
}

function createErrorMessage(element, message) {
    const error = document.createElement('span');
    error.textContent = message;
    error.className = 'error';
    appendElement(element, error);
    return error;
}

const fullNameInput = document.getElementById('full-name');
let fullNameError = null;

fullNameInput.addEventListener('focusout', (_) => {
    validateNameInput();
});

function validateNameInput() {
    const err = checkFIO(fullNameInput.value);
    if (err != null) {
        if (fullNameError != null) {
            fullNameError.remove();
        }
        fullNameError = createErrorMessage(fullNameInput, err);
    } else if (fullNameError != null) {
        fullNameError.remove();
        fullNameError = null;
        return false;
    }
    return true;
}

const genderRadiosDiv = document.getElementById('gender-radios');
const genderRadios = genderRadiosDiv.querySelectorAll('input');
let genderError = null;

function validateGenderRadios() {
    let unchecked = 0;
    for (let i = 0; i < genderRadios.length; i += 1) {
        if (!genderRadios[i].checked) {
            unchecked += 1;
        }
    }
    if (genderRadios.length != 0 && unchecked == genderRadios.length) {
        if (genderError == null) {
            const br = document.createElement('br');
            const error = document.createElement('span');
            error.textContent = 'Выберите один из элементов.';
            error.className = 'error';
            appendElement(genderRadiosDiv, error);
            appendElement(error, br);
            genderError = error;
        }
        return true;
    }
    if (genderError != null) {
        genderError.nextSibling.remove();
        genderError.remove();
        genderError = null;
    }
    return false;
}

genderRadios.forEach(r => {
    r.addEventListener('change', _ => {
        validateGenderRadios();
    });
});

const ageSelect = document.getElementById('age');
let ageError = null;

function validateAgeSelect() {
    if (ageSelect.value === '') {
        if (ageError != null) {
            ageError.nextSibling.remove();
            ageError.remove();
        }
        const br = document.createElement('br');
        const error = document.createElement('span');
        error.textContent = 'Выберите один из пунктов.';
        error.className = 'error';
        appendElement(ageSelect, error);
        prependElement(error, br);
        ageError = br;
    } else if (ageError != null) {
        ageError.nextSibling.remove();
        ageError.remove();
        ageError = null;
        return false;
    }
    return true;
}

ageSelect.addEventListener('change', _ => {
    validateAgeSelect();
});

const birthdayInput = document.getElementById('birthday-date-input');
let birthdayError = null;

birthdayInput.addEventListener('focusout', (_) => {
    validateBirthdayInput();
});

birthdayInput.addEventListener('focusin', (_) => {
    validateBirthdayInput();
});

function validateBirthdayInput() {
    const err = checkBirthdayDate(birthdayInput.value);
    if (err != null) {
        if (birthdayError != null) {
            birthdayError.remove();
        }
        birthdayError = createErrorMessage(birthdayInput, err);
    } else if (birthdayError != null) {
        birthdayError.remove();
        birthdayError = null;
        return false;
    }
    return true;
}

const emailInput = document.getElementById('email');
let emailError = null;

emailInput.addEventListener('focusout', (_) => {
    validateEmailInput();
});

function validateEmailInput() {
    if (emailInput.value === '') {
        if (emailError != null) {
            emailError.remove();
        }
        emailError = createErrorMessage(emailInput, 'Введите электронную почту.');
    } else if (emailError != null) {
        emailError.remove();
        emailError = null;
        return false;
    }
    return true;
}

const phoneInput = document.getElementById('phone');
let phoneError = null;

phoneInput.addEventListener('focusout', (_) => {
    validatePhoneNumberInput();
});

function validatePhoneNumberInput() {
    const err = checkPhoneNumber(phoneInput.value);
    if (err != null) {
        if (phoneError != null) {
            phoneError.remove();
        }
        phoneError = createErrorMessage(phoneInput, err);
    } else if (phoneError != null) {
        phoneError.remove();
        phoneError = null;
        return false;
    }
    return true;
}

const textInput = document.getElementById('text');
let textError = null;

textInput.addEventListener('focusout', (_) => {
    validateTextInput();
});

function validateTextInput() {
    if (textInput.value === '') {
        if (textError != null) {
            textError.remove();
        }
        textError = createErrorMessage(textInput, 'Введите текст письма.');
    } else if (textError != null) {
        textError.remove();
        textError = null;
        return false;
    }
    return true;
}

const form = document.getElementById('callback-form');
form.addEventListener('submit', (e) => {
    let err = false;
    err = validateNameInput();
    err = validateGenderRadios();
    err = validateAgeSelect();
    err = validateBirthdayInput();
    err = validateEmailInput();
    err = validatePhoneNumberInput();
    err = validateTextInput();
    if (err) {
        e.preventDefault();
        return false;
    }
    return true;
}, false);

form.addEventListener('reset', _ => {
    if (fullNameError != null) {
        fullNameError.remove();
        fullNameError = null;
    }
    if (genderError != null) {
        genderError.nextSibling.remove();
        genderError.remove();
        genderError = null;
    }
    if (ageError != null) {
        ageError.nextSibling.remove();
        ageError.remove();
        ageError = null;
    }
    if (birthdayError != null) {
        birthdayError.remove();
        birthdayError = null;
    }
    if (emailError != null) {
        emailError.remove();
        emailError = null;
    }
    if (phoneError != null) {
        phoneError.remove();
        phoneError = null;
    }
    if (textError != null) {
        textError.remove();
        textError = null;
    }

});

function checkFIO(fio) {
    if (fio === '') {
        return 'Введите имя.';
    }
    const words = fio.split(' ');
    if (words.length !== 3) {
        return 'Фио должно иметь 3 слова.';
    }
    return null;
}

function checkPhoneNumber(phoneNumber) {
    if (phoneNumber.length === 0) {
        return 'Введите номер.';
    }

    if ((phoneNumber[0] !== '+' || phoneNumber[0] !== '+') && (phoneNumber[1] !== '7' || phoneNumber[1] !== '3')) {
        return 'Номер телефона должен начинать с +7 или +3.';
    }

    let digitCount = 0;
    let hasWhiteSpace = false;
    let otherCount = 0;
    for (let i = 1; i < phoneNumber.length; i += 1) {
        const ch = phoneNumber.charAt(i);
        if (ch >= '0' && ch <= 9) {
            digitCount += 1;
        } else if (ch === ' ' || ch === '\n' || ch === '\t' || ch === '\r') {
            hasWhiteSpace = true;
        } else {
            otherCount += 1;
        }
    }

    if (hasWhiteSpace) {
        return 'Номер телефона не должен иметь пробелов.';
    }

    if (otherCount !== 0) {
        return 'Номер телефона может иметь только цифры и символ ' + '.';
    }

    if (digitCount < 9 || digitCount > 11) {
        return 'Номер телефона должен иметь от 9 до 11 цифр.';
    }

    return null;
}

function checkBirthdayDate(date) {
    if (date === '') {
        return 'Выберите дату.';
    }
    const today = new Date();
    const tomorrow = new Date(today.getFullYear(), today.getMonth(), today.getDate()).getTime();
    const [day, month, year] = date.split('.');
    const pickedDate = new Date(year, month - 1, day).getTime();
    if (pickedDate > tomorrow) {
        return 'Выберите дату в прошлом.';
    } else if (pickedDate == tomorrow) {
        return 'Вам 0 лет.';
    }
    return null;
}
