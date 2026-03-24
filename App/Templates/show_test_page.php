<?php
use App\Models\Test\TestResult;
/*
* @var array<TestResult> $results
*/
?>
<section class="content-container">
    <br/>
    <?php if (count($results) === 0): ?>
        <h1>Тест еще не прошли ни разу :(</h1>
    <?php else: ?>
        <h1>Результаты теста разных пользователей</h1>
        <br/>
        <?php foreach ($results as $res): ?>
            <h2><?= $res->fio ?></h2>
            <p>UTC <?= $res->format() ?></p>
            <?php if ($res->is_correct()): ?>
                <p style="color:green">Решено верно</p>
            <?php else: ?>
                <p style="color:red">Есть ошибки</p>
                <ul>
                    <?= isset($res->lim_answ) ? "<li>Предел: {$res->lim_answ}</li>" : '' ?>
                    <?= isset($res->series_answ) ? "<li>Ряд: {$res->series_answ}</li>" : '' ?>
                    <?= isset($res->hard_answ) ? "<li>Сложный вопрос: {$res->hard_answ}</li>" : '' ?>
                </ul>
            <?php endif ?>
            <hr/>
        <?php endforeach ?>
    <?php endif ?>
</section>
