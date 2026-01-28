$(document).ready(function() {
    const calendar = $('#calendar');
    const input = $('#birthday-date-input');
    const yearSelect = $('#year-select');
    const monthSelect = $('#month-select');
    let today = new Date();
    let selectedDate = today;

    function selectDate(date) {
        input.val(formatDate(date));
        selectedDate = date;
        monthSelect.val(date.getMonth());
        yearSelect.val(date.getFullYear());
    }

    function showCalendar() {
        calendar.addClass('show');
        selectDate(selectedDate);
        renderCalendar(selectedDate.getFullYear(), selectedDate.getMonth());
    }

    function checkDateGreater(d1, d2) {
        const d1Time = d1.getTime();
        const d2Time = d2.getTime();
        return d1Time > d2Time;
    }

    function renderCalendar(year, month) {
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();

        let firstDayOfWeek = firstDay.getDay();
        firstDayOfWeek = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1;

        const calendarBody = $('#calendar-body');
        calendarBody.empty();

        for (let i = 0; i < firstDayOfWeek; i += 1) {
            $('<div>')
                .addClass('blank-day')
                .appendTo(calendarBody);
        }

        for (let i = 1; i <= daysInMonth; i += 1) {
            let day = null;
            if (checkDateGreater(new Date(year, month, i), today)) {
                day = $('<div>')
                    .addClass('day', 'future')
                    .text(String(i));
            } else {
                day = $('<button>')
                    .addClass('day')
                    .text(String(i));

                if (year === today.getFullYear()
                    && month === today.getMonth()
                    && i === today.getDate()) {
                    day.addClass('today');
                }

                if (selectedDate
                    && year === selectedDate.getFullYear()
                    && month === selectedDate.getMonth()
                    && i === selectedDate.getDate()) {
                    day.addClass('selected');
                }
                day.click(function() {
                    selectDate(new Date(year, month, i));
                    input.focus();
                });
            }
            calendarBody.append(day);
        }

        const totalCells = 35;
        const remainingCells = totalCells - (firstDayOfWeek + daysInMonth);
        for (let i = 0; i < remainingCells; i += 1) {
            $('<div>')
                .addClass('blank-day')
                .appendTo(calendarBody);
        }
    }

    function formatDate(date) {
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        return `${day}.${month}.${year}`;
    }

    selectDate(selectedDate);

    for (let year = 1900; year <= today.getFullYear(); year += 1) {
        $('<option>')
            .attr('value', year)
            .text(String(year))
            .appendTo(yearSelect);
    }
    yearSelect.val(today.getFullYear());

    input.on('focusin', function() {
        showCalendar(calendar);
    });

    $(document).click(function(e) {
        const has = calendar.get(0).contains(e.target);
        const is = e.target === input.get(0);
        if (!has && !is) {
            calendar.removeClass('show');
        }
    });

    yearSelect.on('change', function() {
        const year = Number($(this).val());
        const month = Number(monthSelect.val());
        renderCalendar(year, month);
    });

    monthSelect.on('change', function() {
        const month = Number($(this).val());
        const year = Number(yearSelect.val());
        renderCalendar(year, month);
    });

});

