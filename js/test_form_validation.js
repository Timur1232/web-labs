import { validateForm } from "./form_checker.js";

function isFloatNumber(str) {
    if (str.length == 0) {
        return false;
    }
    let hasPeriod = false;
    for (let i = 0; i < str.length; i += 1) {
        const ch = str.charAt(i);
        if (ch === '.' || ch == ',') {
            if (hasPeriod) {
                return false;
            }
            hasPeriod = true;
            continue;
        }
        if (!(ch >= '0' && ch <= '9')) {
            return false;
        }
    }
    return true;
}

function validateTestForm(form) {
    const answer = form.answer_field;
    const words = answer.value.trim().split(' ');

    for (let i = 0; i < words.length; i += 1) {
        if (isFloatNumber(words[i])) {
            return true;
        }
    }
    alert("Ответ должен содержать хотя бы одно вещественное число.");
    return false;
}

const form = document.querySelector("#test-form");
form.addEventListener('submit', (event) => {
    if (!validateForm(form) || !validateTestForm(form)) {
        event.preventDefault();
        return false;
    }
    return true;
}, false);
