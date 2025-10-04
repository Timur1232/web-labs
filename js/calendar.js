const calendar = document.getElementById('calendar');
const input = document.getElementById('birthday-date-input');
const yearSelect = document.getElementById('year-select');
const monthSelect = document.getElementById('month-select');
let today = new Date();
let selectedDate = today;
selectDate(selectedDate);

for (let year = 1900; year <= today.getFullYear(); year += 1) {
    const yearOption = document.createElement('option');
    yearOption.value = year;
    yearOption.textContent = String(year);
    yearSelect.appendChild(yearOption);
}
yearSelect.value = today.getFullYear();

input.addEventListener('focusin', () => {
    showCalendar();
});

document.addEventListener('click', (event) => {
    if (!calendar.contains(event.target) && event.target !== input) {
        hideCalendar();
    }
});

function showCalendar() {
    calendar.classList.add('show');
    renderCalendar(today.getFullYear(), today.getMonth());
}

function hideCalendar() {
    calendar.classList.remove('show');
}


function renderCalendar(year, month) {
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();

    let firstDayOfWeek = firstDay.getDay();
    firstDayOfWeek = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1;

    const calendarBody = document.getElementById('calendar-body');
    calendarBody.innerHTML = '';

    for (let i = 0; i < firstDayOfWeek; i += 1) {
        const blankDay = document.createElement('div');
        blankDay.className = 'blank-day';
        calendarBody.appendChild(blankDay);
    }

    for (let i = 1; i <= daysInMonth; i += 1) {
        const day = document.createElement('button');
        day.className = 'day';
        day.textContent = i;

        if (year === today.getFullYear() &&
            month === today.getMonth() &&
            i === today.getDate()) {
            day.classList.add('today');
        }

        if (selectedDate &&
            year === selectedDate.getFullYear() &&
            month === selectedDate.getMonth() &&
            i === selectedDate.getDate()) {
            day.classList.add('selected');
        }

        day.addEventListener('click', () => selectDate(new Date(year, month, i)));
        calendarBody.appendChild(day);
    }

    const totalCells = 35;
    const remainingCells = totalCells - (firstDayOfWeek + daysInMonth);
    for (let i = 0; i < remainingCells; i += 1) {
        const blankDay = document.createElement('div');
        blankDay.className = 'blank-day';
        calendarBody.appendChild(blankDay);
    }
}

function selectDate(date) {
    input.value = formatDate(date);
    selectedDate = date;
    monthSelect.value = date.getMonth();
    renderCalendar(date.getFullYear(), date.getMonth());
}

function formatDate(date) {
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    return `${day}.${month}.${year}`;
}

yearSelect.addEventListener('change', (event) => {
    const year = Number(event.currentTarget.value);
    const month = Number(monthSelect.value);
    renderCalendar(year, month);
});

monthSelect.addEventListener('change', (event) => {
    const month = Number(event.currentTarget.value);
    const year = Number(yearSelect.value);
    renderCalendar(year, month);
});


