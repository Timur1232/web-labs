<section class="form-container">
    <form id="callback-form" class="callback-form shadow rounded" method="post"
        action="mailto:timur.univercity@gmail.com" enctype="text/plain">
        <label for="full-name">ФИО:</label><br>
        <input id="full-name" class="input-text" type="text" name="full-name" autofocus /><br>

        <label for="gender">Пол:</label><br>
        <div id="gender-radios">
            <input class="input-radio" type="radio" name="gender" id="male" value="male" />
            <label for="male">Мужской</label>
            <input class="input-radio" type="radio" name="gender" id="female" value="female" />
            <label for="female">Женский</label>
        </div>

        <label for="burthday">Дата рождения:</label><br>
        <input class="input-text" type="text" name="birthday" id="birthday-date-input"></input><br>
        <div id="calendar" class="calendar shadow rounded">
            <div class="calendar-controls">
                <select id="month-select" class="month-select">
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
                <select id="year-select" class="year-select"></select>
            </div>
            <ul class="calendar-header">
                <li class="calendat-weekday">Sun</li>
                <li class="calendat-weekday">Mon</li>
                <li class="calendat-weekday">Tues</li>
                <li class="calendat-weekday">Wed</li>
                <li class="calendat-weekday">Thu</li>
                <li class="calendat-weekday">Fri</li>
                <li class="calendat-weekday">Sat</li>
            </ul>
            <div id="calendar-body" class="calendar-body"></div>
        </div>

        <label for="sender-email">Email:</label><br>
        <input id="email" class="input-text" type="email" name="sender-email" /><br>

        <label for="phone">Телефон:</label><br>
        <input id="phone" class="input-text" type="tel" name="phone" /><br>

        <label for="callback-text">Текст письма:</label><br>
        <textarea id="text" name="callback-text" rows="5"></textarea>

        <div class="buttons">
            <input class="button-submit" type="submit" value="Отправить" />
            <input class="button-reset" type="reset" value="Очистить форму" />
        </div>
    </form>
</section>
