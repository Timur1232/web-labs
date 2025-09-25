export function validateForm(form) {
    const textareas = form.querySelectorAll("textarea");
    const radios = form.querySelectorAll(".input-radio");
    const inputs = form.querySelectorAll(".input-text");
    const selects = form.querySelectorAll("select");

    let err = false;

    textareas.forEach((element) => {
        if (element.value === "") {
            showError(element, "Textarea: Заполните текстовое поле.");
            err = true;
        }
    });

    inputs.forEach((element) => {
        if (element.value === "") {
            showError(element, "Input-text: Заполните текстовое поле.");
            err = true;
        }
    });

    selects.forEach((element) => {
        if (element.value === "") {
            showError(element, "Select: Выберите один из элементов.");
            err = true;
        }
    });

    let unchecked = 0;
    for (let i = 0; i < radios.length; i += 1) {
        if (!radios[i].checked) {
            unchecked += 1;
        }
    }

    if (radios.length != 0 && unchecked == radios.length) {
        showError(radios[0], "Input-radio: Выберите один из элементов.");
        err = true;
    }

    if (err) {
        return false;
    }
    return true;
}

function showError(element, message) {
    alert(message);
    element.focus();
}
