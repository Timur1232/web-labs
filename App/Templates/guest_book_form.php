<?php
use App\Models\GuestBook\Messege;
use App\Core\Model\FileCSVModel;
/*
* @var FileCSVModel $model
*/
?>
<section id="guest_book_form_cantainer" class="content-container">
    <form class="callback-form"
        action="/guest_book" method="post" enctype="text/plain"
        hx-post="/guest_book"
        hx-target="#guest_book_form_cantainer"
        hx-swap="outerHTML"
    >
        <label for="fio">ФИО:</label>
        <input type="text" class="input-text" id="fio" name="fio"></input>
        <br/>
        <label for="email">Email:</label>
        <input type="email" class="input-text" id="email" name="email"></input>
        <br/>
        <label for="text">Отзыв:</label>
        <textarea id="text" name="text" row="5"></textarea>
        <br/>
        <input class="button-submit" type="submit" value="Отправить" />
        <input class="button-reset" type="reset" value="Очистить форму" />
    </form>

    <style>
    td, tr, th, table {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 5px;
    }
    </style>
    <table style="width: 60%; background-color: var(--main-light-color);">
        <tr>
            <th style="width:30%;max-width:50%;">Отправитель</th><th>Отзыв</th>
        </tr>
        <?php
            $res = $model->find_all(Messege::class);
            if (!$res->ok) return $res;
            foreach (array_reverse($res->val) as $r) {
                if (!isset($r->datestr)) continue;
            ?>
            <tr>
                <td><?= $r->fio ?? '<Без имени>' ?></td>
                <td rowspan="2"><?= $r->text ?? '' ?></td>
            </tr>
            <tr>
                <td>
                    Отправлено: UTC <?= $r->format() ?><br/>
                    <a href="mailto:<?= $r->email ?? '' ?>">
                        <?= $r->email ?? '' ?>
                    </a>
                </td>
            </tr>
        <?php }
        ?>
    </table>
</section>
