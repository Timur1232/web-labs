<?php
/*
* @var ?string $msg
*/
?>
<section id="admin_guest_book_form" class="content-container">
    <h2>Загрузить файл с записями на сервер</h2>
    <form action="/admin/blog/load" method="post"
        enctype="multipart/form-data"
        class="callback-form"
    >
        <label for="posts">CSV файл постов</label><br/>
        <input type="file" id="posts" name="posts" accept=".csv" required /><br/>
        <input class="button-submit" type="submit" value="Добавить"/>
    </form>
    <span id="msg"><?= $msg ?? '' ?></span>
</section>
