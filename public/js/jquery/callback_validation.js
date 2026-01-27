let fullNameError = null;
let genderError = null;
let birthdayError = null;
let emailError = null;
let phoneError = null;
let textError = null;

$(document).ready(function() {
    $('#full-name').on('focusout', function() {
        validateNameInput();
    });

    $('#gender-radios').on('change', function() {
        validateGenderRadios();
    });

    $('#birthday-date-input').on('focusout', function() {
        validateBirthdayInput();
    }).on('focusin', function() {
        validateBirthdayInput();
    });

    $('#email').on('focusout', function() {
        validateEmailInput();
    });

    $('#phone').on('focusout', function() {
        validatePhoneNumberInput();
    });

    $('#text').on('focusout', function() {
        validateTextInput();
    });

    $('#callback-form').on('submit', function(e) {
        let err = false;
        err |= validateNameInput();
        err |= validateGenderRadios();
        err |= validateBirthdayInput();
        err |= validateEmailInput();
        err |= validatePhoneNumberInput();
        err |= validateTextInput();
        if (err) {
            e.preventDefault();
            return false;
        }
        return true;
    }).on('reset', function() {
        if (fullNameError != null) {
            fullNameError.remove();
            fullNameError = null;
        }
        if (genderError != null) {
            genderError.next().remove();
            genderError.remove();
            genderError = null;
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
});

function validateNameInput() {
    const fullNameInput = $('#full-name');
    const err = checkFIO(fullNameInput.val());
    if (err != null) {
        if (fullNameError != null) {
            fullNameError.remove();
        }
        fullNameError = createErrorMessage(fullNameInput, err);
    } else {
        if (fullNameError != null) {
            fullNameError.remove();
            fullNameError = null;
        }
        return false;
    }
    return true;
}

function validateGenderRadios() {
    const genderRadiosDiv = $('#gender-radios');
    const genderRadios = genderRadiosDiv.find('input:checked');
    let unchecked = genderRadios.get().length == 0;

    if (unchecked) {
        if (genderError == null) {
            genderError = $('<span>')
                .text('Выберите один из элементов.')
                .addClass('error')
                .appendTo(genderRadiosDiv);
            genderError.before($('<br>'), $('<br>'));
        }
        return true;
    }
    if (genderError != null) {
        genderError.next().remove();
        genderError.remove();
        genderError = null;
    }
    return false;
}

function validateBirthdayInput() {
    const birthdayInput = $('#birthday-date-input');
    const err = checkBirthdayDate(birthdayInput.val());
    if (err != null) {
        if (birthdayError != null) {
            birthdayError.remove();
        }
        birthdayError = createErrorMessage(birthdayInput, err);
    } else {
        if (birthdayError != null) {
            birthdayError.remove();
            birthdayError = null;
        }
        return false;
    }
    return true;
}

function validateEmailInput() {
    const emailInput = $('#email');
    if (emailInput.val() === '') {
        if (emailError != null) {
            emailError.remove();
        }
        emailError = createErrorMessage(emailInput, 'Введите электронную почту.');
    } else {
        if (emailError != null) {
            emailError.remove();
            emailError = null;
        }
        return false;
    }
    return true;
}

function validatePhoneNumberInput() {
    const phoneInput = $('#phone');
    const err = checkPhoneNumber(phoneInput.val());
    if (err != null) {
        if (phoneError != null) {
            phoneError.remove();
        }
        phoneError = createErrorMessage(phoneInput, err);
    } else {
        if (phoneError != null) {
            phoneError.remove();
            phoneError = null;
        }
        return false;
    }
    return true;
}

function validateTextInput() {
    const textInput = $('#text');
    if (textInput.val() === '') {
        if (textError != null) {
            textError.remove();
        }
        textError = createErrorMessage(textInput, 'Введите текст письма.');
    } else {
        if (textError != null) {
            textError.remove();
            textError = null;
        }
        return false;
    }
    return true;
}

// utils

function createErrorMessage(element, message) {
    const err = $('<span>')
        .text(message)
        .addClass('error');
    element.after(err);
    return err;
}

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

    if (phoneNumber.charAt(0) !== '+' || (phoneNumber.charAt(1) !== '7' && phoneNumber.charAt(1) !== '3')) {
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
        return 'Номер телефона может иметь только цифры и символ \'+\'.';
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
