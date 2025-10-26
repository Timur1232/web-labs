function formatClock(date) {
    const months = [
        'Января',
        'Февраля',
        'Марта',
        'Апреля',
        'Мая',
        'Июня',
        'Июля',
        'Августа',
        'Сентября',
        'Октября',
        'Ноября',
        'Декабря',
    ];
    const month = months[date.getMonth()];
    return `${date.getDate()} ${month} ${date.getFullYear()}`;
}

function updateClock() {
    const now = new Date();
    $('.clock').text(formatClock(now));
}

$(document).ready(function() {
    updateClock();

    setInterval(() => {
        updateClock();
    }, 1000);
});
