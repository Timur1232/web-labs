export function validateForm(form) {
    const textareas = $(form).find("textarea");
    const radios = $(form).find(".input-radio:checked");
    const inputs = $(form).find(".input-text");
    const selects = $(form).find("select");

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

    let unchecked = radios.get().length == 0;
    if (unchecked) {
        showError($(form).find(".input-radio").get(0), "Input-radio: Выберите один из элементов.");
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
