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

function updateClock(element) {
    const now = new Date();
    element.textContent = formatClock(now);
}

const clock = document.getElementById('clock');
updateClock(clock);

setInterval(() => {
    updateClock(clock);
}, 1000);
