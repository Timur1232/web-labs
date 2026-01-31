<?php

require_once 'app/models/test_result.php';
use App\Models\TestModel;

/*
* @var TestModel $model
*/
?>
<section class="content-container">
    <div class="content-block">
        <?php if (!$model->has_errors()): ?>
            <h2>Молодец! Возми с полки пирожок.</h2>
        <?php else: ?>
            <h2>Все х***я, давай по новой.</h2>
            <?php if ($model->has_lim_errors()): ?>
                <p>Вопрос №1</p>
                <ul>
                    <?php foreach ($model->get_lim_messeges() as $msg): ?>
                        <li><?= $msg ?></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
            <?php if ($model->has_series_errors()): ?>
                <p>Вопрос №2</p>
                <ul>
                    <?php foreach ($model->get_series_messeges() as $msg): ?>
                        <li><?= $msg ?></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
            <?php if ($model->has_hard_errors()): ?>
                <p>Вопрос №3</p>
                <ul>
                    <?php foreach ($model->get_hard_messeges() as $msg): ?>
                        <li><?= $msg ?></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
        <?php endif ?>
        <div style="display: flex;">
            <a href="/study/test">Пройти еще раз (зачем?)</a>
            <a href="/">На главную</a>
        </div>
    </div>
</section>
