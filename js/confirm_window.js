function createConfirmWindow(message, callback) {
    const div = $('<div>')
        .addClass('confirm-window')
        .text(message);

    const buttons = $('<div>').addClass('buttons');
    $('<button>Подтвердить</button>')
        .click(function() {
            alert('confirmed');
            callback();
            deleteConfirmWindow();
        }).appendTo(buttons);
    $('<button>Отменить</button>')
        .click(function() {
            alert('canceled');
            deleteConfirmWindow();
        }).appendTo(buttons);

    buttons.appendTo(div);
    div.appendTo('main');
}

function deleteConfirmWindow() {
    $('.confirm-window').remove();
}

$(document).ready(function() {
    $('#test-confirm').click(function() {
        createConfirmWindow('Подтвердите действие', () => {
            alert('callback');
        });
    });
});
