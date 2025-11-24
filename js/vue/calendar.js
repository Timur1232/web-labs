function checkDateGreater(d1, d2) {
    const d1Time = d1.getTime();
    const d2Time = d2.getTime();
    return d1Time > d2Time;
}

function getDaysInMonth(year, month, selectedDate) {
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysAmountInMonth = lastDay.getDate();
    const today = new Date();

    let firstDayOfWeek = firstDay.getDay();
    firstDayOfWeek = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1;

    let daysInMonth = [];

    for (let i = 0; i < firstDayOfWeek; i += 1) {
        daysInMonth.push({ active: false });
    }

    for (let i = 1; i <= daysAmountInMonth; i += 1) {
        let day = null;
        if (checkDateGreater(new Date(year, month, i), today)) {
            day = {
                dayNum: i,
                active: false,
                class: "future",
            };
        } else {
            day = {
                dayNum: i,
                active: true,
                class: "day",
            };

            if (selectedDate
                && year === selectedDate.getFullYear()
                && month === selectedDate.getMonth()
                && i === selectedDate.getDate()) {
                day.class += " selected";
            } else if (year === today.getFullYear()
                && month === today.getMonth()
                && i === today.getDate()) {
                day.class += " today";
            }
        }
        daysInMonth.push(day);
    }

    const totalCells = 35;
    const remainingCells = totalCells - (firstDayOfWeek + daysAmountInMonth);
    for (let i = 0; i < remainingCells; i += 1) {
        daysInMonth.push({ active: false });
    }

    return daysInMonth;
}

export default {
    data() {
        return {
            month: 0,
            year: 0,
            day: 0,
            selectedDate: null,
            years: [],
            daysInMonth: [],
        };
    },
    watch: {
        month(newMonth) {
            this.daysInMonth = getDaysInMonth(this.year, Number(newMonth), this.selectedDate);
        },
        year(newYear) {
            this.daysInMonth = getDaysInMonth(Number(newYear), this.month, this.selectedDate);
        },

    },
    created() {
        const today = new Date();
        for (let year = today.getFullYear(); year >= 1900; year -= 1) {
            this.years.push(year);
        }
        if (this.selectedDateStr) {
            const [day, month, year] = this.selectedDateStr.split(".");
            this.month = Number(month) - 1;
            this.year = Number(year);
            this.day = Number(day);
        } else {
            this.month = today.getMonth();
            this.year = today.getFullYear();
            this.day = today.getDate();
        }
        this.selectedDate = new Date(this.year, this.month, this.day);
        this.daysInMonth = getDaysInMonth(this.year, this.month, this.selectedDate);
    },
    emits: ["date-selected"],
    props: ["selectedDateStr"],
    methods: {
        dateSelected(day) {
            this.day = day;
            this.selectedDate = new Date(this.year, this.month, this.day);
            this.daysInMonth = getDaysInMonth(this.year, this.month, this.selectedDate);
            this.$emit("date-selected", this.day, this.month + 1, this.year);
        },
    },
    template: `
        <div class="calendar shadow rounded">
            <div class="calendar-controls">
                <select id="month-select" v-model="month" class="month-select">
                    <option value="0">January</option>
                    <option value="1">February</option>
                    <option value="2">March</option>
                    <option value="3">April</option>
                    <option value="4">May</option>
                    <option value="5">June</option>
                    <option value="6">July</option>
                    <option value="7">August</option>
                    <option value="8">September</option>
                    <option value="9">October</option>
                    <option value="10">November</option>
                    <option value="11">December</option>
                </select>
                <select id="year-select" v-model="year" class="year-select">
                    <option v-for="optionYear in years"
                        :value="optionYear">
                        {{ optionYear }}
                    </option>
                </select>
            </div>
            <ul class="calendar-header">
                <li class="calendar-weekday">Sun</li>
                <li class="calendar-weekday">Mon</li>
                <li class="calendar-weekday">Tues</li>
                <li class="calendar-weekday">Wed</li>
                <li class="calendar-weekday">Thu</li>
                <li class="calendar-weekday">Fri</li>
                <li class="calendar-weekday">Sat</li>
            </ul>
            <div class="calendar-body">
                <template v-for="day in daysInMonth">
                    <button v-if="day.active"
                        :class="day.class"
                        @click.stop.prevent="dateSelected(day.dayNum)">{{ day.dayNum }}</button>
                    <div v-else class="blank-day"></div>
                </template>
            </div>
        </div>
`
};
