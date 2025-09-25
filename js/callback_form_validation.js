import { validateForm } from "./form_checker.js";

function validatePhoneNumber(phoneNumber) {
    if (phoneNumber.length === 0) {
        return false;
    }

    if ((phoneNumber[0] !== '+' || phoneNumber[0] !== '+') && (phoneNumber[1] !== '7' || phoneNumber[1] !== '3')) {
        alert("Номер телефона должен начинать с +7 или +3.");
        return false;
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
        alert("Номер телефона не должен иметь пробелов.");
        return false;
    }

    if (otherCount !== 0) {
        alert("Номер телефона может иметь только цифры и символ '+'.");
        return false;
    }

    if (digitCount < 9 || digitCount > 11) {
        alert("Номер телефона должен иметь от 9 до 11 цифр.");
        return false;
    }

    return true;
}

function validateFIO(fio) {
    const words = fio.split(' ');
    if (words.length !== 3) {
        alert("Фио должно иметь 3 слова.");
        return false;
    }
    return true;
}

function validateCallbackForm(form) {
    const phoneNumber = form.phone;
    const fio = form.full_name;
    if (!validateFIO(fio.value)) {
        fio.focus();
        return false;
    }
    if (!validatePhoneNumber(phoneNumber.value)) {
        phoneNumber.focus();
        return false;
    }
    return true;
}

const form = document.querySelector(".callback-form");
form.addEventListener('submit', (event) => {
    if (!validateForm(form) || !validateCallbackForm(form)) {
        event.preventDefault();
        return false;
    }
    return true;
}, false);
