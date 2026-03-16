<?php
namespace App\Views;
use App\Core\View\Component;
use App\Core\View\View;
use App\Models\GuestBook\Messege;

final class GuestBookView {
    /**
     * @param array<int,Messege> $messeges
     */
    public static function form(array $messeges): Component {
        return View::func(function () use ($messeges) {
            ob_start();
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
                        foreach ($messeges as $m) {
                            if (!isset($m->datestr)) continue;
                        ?>
                        <tr>
                            <td><?= $m->fio ?? '<Без имени>' ?></td>
                            <td rowspan="2"><?= $m->text ?? '' ?></td>
                        </tr>
                        <tr>
                            <td>
                                Отправлено: <?= $m->get_date()->format('d.m.Y H:i') ?><br/>
                                <a href="mailto:<?= $m->email ?? '' ?>">
                                    <?= $m->email ?? '' ?>
                                </a>
                            </td>
                        </tr>
                    <?php }
                    ?>
                </table>
            </section>
            <?php
            return ob_get_clean();
        });
    }
}
