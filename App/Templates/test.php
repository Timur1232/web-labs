<section class="content-container">
    <h1 class="content-title">Тест по Высшей Математике</h1>
    <form id="test-form"
        action="/study/test"
        method="post"
        hx-post="/study/test"
        hx-target="main"
    >
        <article class="test-question content-block" id="question1">
            <label for="fio">ФИО:</label>
            <input class="input-text" type="text" id="fio" name="fio" required></input>
        </article>
        <article class="test-question content-block" id="question1">
            <h2>1. Вычислите предел:</h2>
            <img src="/public/media/limit.png" alt="lim_{x->0} sin(5x)/x" title="lim_x->0 sin(5x)/x" />
            <div class="question-form">
                <textarea name="lim" rows="4" required></textarea>
            </div>
        </article>
        <article class="test-question content-block">
            <h2>2. Выберите верное утверждение о сходимости ряда:</h2>
            <div class="question-form">
                <input id="series1" class="input-radio" type="radio" name="series" value="1" required />
                <label for="series1">1) Ряд <img src="/public/media/sum1.png" alt="sum_{n=1}^{\infinity} 1/n" />
                    является сходящимся.</label><br>
                <input id="series2" class="input-radio" type="radio" name="series" value="2" required />
                <label for="series2">2) Ряд <img src="/public/media/sum2.png" alt="sum_{n=1}^{\infinity} 1/(n^2)" />
                    является сходящимся.</label><br>
            </div>
        </article>
        <article class="test-question content-block">
            <h2>3. Выберите правильный ответ:</h2>
            <p>2 + 2 = ?</p>
            <div class="question-form">
                <select size="1" name="hard_one" required>
                    <option value="">--Выберите ответ--</option>
                    <option value="22">22</option>
                    <option value="ce">Compilation Error</option>
                    <option value="4">4</option>
                </select><br>
            </div>
        </article>
        <div class="buttons">
            <input class="button-submit" type="submit" value="Отправить" />
            <input class="button-reset" type="reset" value="Очистить форму" />
        </div>
    </form>
    <br/>
</section>
