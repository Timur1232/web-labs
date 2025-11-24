import calendar from "./calendar.js"

const app = Vue.createApp({
    data() {
        return {
            formData: {
                fullName: "",
                gender: "",
                birthday: "",
                email: "",
                phone: "",
                text: "",
            },
            genders: Object.freeze({
                M: "Мужской",
                F: "Женский",
            }),
            calendarStatus: 0,
            errors: {
                fullName: null,
                gender: null,
                birthday: null,
                email: null,
                phone: null,
                text: null,
            },
        };
    },
    methods: {
        closeCalendar() {
            if (this.calendarStatus == 1) {
                this.calendarStatus = 2;
            } else {
                this.calendarStatus = 0;
                this.validateBirthdayDate();
            }
        },
        setSelectedDate(day, month, year) {
            this.formData.birthday = `${day}.${month}.${year}`;
            this.validateBirthdayDate();
        },
        openCalendar() {
            this.calendarStatus = 1;
        },
        validateFullName() {
            if (this.formData.fullName === "") {
                this.errors.fullName = "Введите имя.";
                return true;
            }
            const words = this.formData.fullName.split(" ");
            if (words.length !== 3) {
                this.errors.fullName = "Фио должно иметь 3 слова.";
                return true;
            }
            this.errors.fullName = null;
            return false;
        },
        validateEmail() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.formData.email)) {
                this.errors.email = "Неверный формат email";
                return true;
            }
            this.errors.email = null;
            return false;
        },
        validatePhoneNumber() {
            if (this.formData.phone.length === 0) {
                this.errors.phone = "Введите номер.";
                return true;
            }

            if (this.formData.phone.charAt(0) !== "+" || (this.formData.phone.charAt(1) !== "7" && this.formData.phone.charAt(1) !== "3")) {
                this.errors.phone = "Номер телефона должен начинать с +7 или +3.";
                return true;
            }

            let digitCount = 0;
            let hasWhiteSpace = false;
            let otherCount = 0;
            for (let i = 1; i < this.formData.phone.length; i += 1) {
                const ch = this.formData.phone.charAt(i);
                if (ch >= "0" && ch <= 9) {
                    digitCount += 1;
                } else if (ch === " " || ch === "\n" || ch === "\t" || ch === "\r") {
                    hasWhiteSpace = true;
                } else {
                    otherCount += 1;
                }
            }

            if (hasWhiteSpace) {
                this.errors.phone = "Номер телефона не должен иметь пробелов.";
                return true;
            }

            if (otherCount !== 0) {
                this.errors.phone = "Номер телефона может иметь только цифры и символ \"+\".";
                return true;
            }

            if (digitCount < 9 || digitCount > 11) {
                this.errors.phone = "Номер телефона должен иметь от 9 до 11 цифр.";
                return true;
            }

            this.errors.phone = null;
            return false;
        },
        validateBirthdayDate() {
            const dateRegex = /\d{1,2}[\.-\/]\d{1,2}[\.-\/]\d{4}/;
            if (!dateRegex.test(this.formData.birthday)) {
                this.errors.birthday = "Неправильный формат даты.";
                return true;
            }
            if (this.formData.birthday === "") {
                this.errors.birthday = "Выберите дату.";
                return true;
            }
            const today = new Date();
            const tomorrow = new Date(today.getFullYear(), today.getMonth(), today.getDate()).getTime();
            const [day, month, year] = this.formData.birthday.split(".");
            const pickedDate = new Date(year, month - 1, day).getTime();
            if (pickedDate > tomorrow) {
                this.errors.birthday = "Выберите дату в прошлом.";
                return true;
            } else if (year == today.getFullYear()) {
                this.errors.birthday = "Вам 0 лет.";
                return true;
            } else {
                this.errors.birthday = null;
                return false;
            }
        },
        validateTextInput() {
            if (this.formData.text === "") {
                this.errors.text = "Введите текст письма";
                return true;
            } else {
                this.errors.text = null;
                return false;
            }
        },
        validateGender() {
            if (this.formData.gender === "") {
                this.errors.gender = "Выберите пол.";
                return true;
            }
            this.errors.gender = null;
            return false;
        },
        validateAll(e) {
            let err = this.validateFullName();
            err |= this.validateEmail();
            err |= this.validatePhoneNumber();
            err |= this.validateBirthdayDate();
            err |= this.validateTextInput();
            err |= this.validateGender();
            if (err) {
                e.preventDefault();
                e.stopPropagation();
            }
        },
        clearErrors() {
            this.errors.fullName = null;
            this.errors.gender = null;
            this.errors.birthday = null;
            this.errors.email = null;
            this.errors.phone = null;
            this.errors.text = null;
        },
    },
    watch: {
        "formData.gender": function(_) {
            this.validateGender();
        }
    },
    computed: {
        isCalendarOpen() {
            return this.calendarStatus != 0;
        },
    },
    components: {
        calendar,
    },
});

app.directive("click-outside", {
    beforeMount: function(element, binding) {
        element.clickOutsideEvent = function(event) {
            if (!(element === event.target || element.contains(event.target))) {
                binding.value(event);
            }
        };
        document.body.addEventListener("click", element.clickOutsideEvent)
    },
    unmounted: function(element) {
        document.body.removeEventListener("click", element.clickOutsideEvent)
    }
});

app.mount("#form");

