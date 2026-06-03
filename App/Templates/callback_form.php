<?php
use App\Views\Callback_View;
use App\Models\Callback_Validator;
/**
* @var Callback_Validator $model
*/?>
<section id="callback_form_cantainer" class="form-container">
    <form id="callback-form" class="callback-form shadow rounded"
        method="post" action="/api/callback" enctype="text/plain"
        hx-post="/api/callback"
        hx-target="#callback_form_cantainer"
        hx-swap="outerHTML"
    >
        <label for="full-name">ФИО:</label><br>
        <input id="full-name" class="input-text" type="text" name="fio" autofocus
            hx-post="/api/callback?f=fio"
            hx-target="#fio_error"
            hx-swap="innerHTML"
            hx-trigger="input changed delay:300ms, keyup[key=='Enter'], focusout"
        />
        <div id="fio_error">
        <?php if (isset($model) && $model->has_fio_errors()) {
            foreach ($model->get_fio_errors() as $err) {
                echo Callback_View::error_tag($err);
            }
        } ?>
        </div>

        <label for="gender-radios">Пол:</label><br>
        <div id="gender-radios">
            <input class="input-radio" type="radio" name="gender" id="male" value="male" />
            <label for="male">Мужской</label>
            <input class="input-radio" type="radio" name="gender" id="female" value="female" />
            <label for="female">Женский</label>
            <div id="gender_error">
            <?php if (isset($model) && $model->has_gender_errors()) {
                foreach ($model->get_gender_errors() as $err) {
                    echo Callback_View::error_tag($err);
                }
            } ?>
            </div>
        </div>

        <label for="birthday-date-input">Дата рождения:</label><br>
        <input class="input-text" type="date" name="birthday" id="birthday-date-input"
            hx-post="/api/callback?f=birthday"
            hx-target="#birthday_error"
            hx-swap="innerHTML"
            hx-trigger="input changed delay:300ms, keyup[key=='Enter'], focusout"
        />
        <div id="birthday_error">
        <?php if (isset($model) && $model->has_birthday_errors()) {
            foreach ($model->get_birthday_errors() as $err) {
                echo Callback_View::error_tag($err);
            }
        } ?>
        </div>
        <!--<br>
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
        </div>-->

        <label for="email">Email:</label><br>
        <input id="email" class="input-text" type="email" name="email"
            hx-post="/api/callback?f=email"
            hx-target="#email_error"
            hx-swap="innerHTML"
            hx-trigger="input changed delay:300ms, keyup[key=='Enter'], focusout"
        />
        <div id="email_error">
        <?php if (isset($model) && $model->has_email_errors()) {
            foreach ($model->get_email_errors() as $err) {
                echo Callback_View::error_tag($err);
            }
        } ?>
        </div>
        <br>

        <label for="phone">Телефон:</label><br>
        <input id="phone" class="input-text" type="tel" name="phone"
            hx-post="/api/callback?f=phone"
            hx-target="#phone_error"
            hx-swap="innerHTML"
            hx-trigger="input changed delay:300ms, keyup[key=='Enter'], focusout"
        />
        <div id="phone_error">
        <?php if (isset($model) && $model->has_phone_errors()) {
            foreach ($model->get_phone_errors() as $err) {
                echo Callback_View::error_tag($err);
            }
        } ?>
        </div>
        <br>

        <label for="text">Текст письма:</label><br>
        <textarea id="text" name="text" rows="5"
            hx-post="/api/callback?f=text"
            hx-target="#text_error"
            hx-swap="innerHTML"
            hx-trigger="input changed delay:300ms, keyup[key=='Enter'], focusout"
        ></textarea>
        <div id="text_error">
        <?php if (isset($model) && $model->has_text_errors()) {
            foreach ($model->get_text_errors() as $err) {
                echo Callback_View::error_tag($err);
            }
        } ?>
        </div>

        <div class="buttons">
            <input class="button-submit" type="submit" value="Отправить" />
            <input class="button-reset" type="reset" value="Очистить форму"
                onclick="document.querySelectorAll('span.error').forEach((el) => el.remove())"
            />
        </div>
    </form>
</section>
