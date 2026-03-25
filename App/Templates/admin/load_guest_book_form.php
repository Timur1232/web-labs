<?php
/*
* @var ?string $msg
*/
?>
<section id="admin_guest_book_form" class="content-container">
    <h2>Скачать файл с записями</h2>
    <a href="/public/messeges.inc" download>Скачать</a>

    <h2>Загрузить файл с записями на сервер</h2>
    <form action="/admin/guest_book/append" method="post"
        enctype="multipart/form-data"
        class="callback-form"
        hx-post="/admin/guest_book/append"
        hx-target="#msg"
        hx-swap="outerHTML"
    >
        <label for="messege">CSV файл сообщений</label><br/>
        <input type="file" id="messege" name="messege" accept=".inc" required /><br/>
        <input class="button-submit" type="submit" value="Добавить"
            formaction="/admin/guest_book/append"
        />
        <input class="button-reset" type="submit" value="Перезаписать"
            onclick="return confirm('Вы уверены? Это перезапишет существующие данные.')"
            formaction="/admin/guest_book/override"
        />
    </form>
    <span id="msg"><?= $msg ?? '' ?></span>
</section>
