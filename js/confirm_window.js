function createConfirmWindow(message, callback) {
    const div = $('<div>')
        .addClass('confirm-window')
        .text(message);

    $('<button>Подтвердить</button>')
        .click(function() {
            alert('confirmed');
            callback();
            deleteConfirmWindow();
        }).appendTo(div);
    $('<button>Отменить</button>')
        .click(function() {
            alert('canceled');
            deleteConfirmWindow();
        }).appendTo(div);

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
