<?php

require_once 'app/models/test_result.php';
use App\Models\TestModel;

/*
* @var TestModel $model
*/
?>
<section class="form-container">
    <style>
    .form-container li {
        list-style-position: inside;
    }
    .form-container h2 {
        width: 100%;
        text-align: center;
    }
    </style>
    <div class="callback-form">
        <?php if (!$model->has_errors()): ?>
            <h2>Молодец! Возми с полки пирожок.</h2>
        <?php else: ?>
            <h2>Нет.</h2>
            <?php if ($model->has_lim_errors()): ?>
                <p>Ошибки в вопросе №1</p>
                <ul>
                    <?php foreach ($model->get_lim_messeges() as $msg): ?>
                        <li class="error"><?= $msg ?></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
            <?php if ($model->has_series_errors()): ?>
                <p>Ошибки в вопросе №2</p>
                <ul>
                    <?php foreach ($model->get_series_messeges() as $msg): ?>
                        <li class="error"><?= $msg ?></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
            <?php if ($model->has_hard_errors()): ?>
                <p>Ошибки в вопросе №3</p>
                <ul>
                    <?php foreach ($model->get_hard_messeges() as $msg): ?>
                        <li class="error"><?= $msg ?></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
        <?php endif ?>
        <table style="width:100%;">
            <tbody>
                <tr>
                    <td style="text-align:center;"><a href="/">На главную</a></td>
                    <td style="text-align:center;"><a href="/study/test">Пройти еще раз (зачем?)</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
