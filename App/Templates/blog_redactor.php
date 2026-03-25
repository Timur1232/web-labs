<?php
/*
* @var string $msg
*/
?>
<section class="content-container">
    <form id="test-form"
        enctype="multipart/form-data"
        action="/admin/blog/post"
        method="post"
    >
        <!-- hx-post="/admin/blog/post" hx-target="#msg" hx-swap="outerHTML" -->
        <label for="title">Заголовок:</label>
        <input class="input-text" type="text" id="title" name="title" required></input>

        <label for="author">Автор:</label>
        <input class="input-text" type="text" id="author" name="author" required></input>

        <label for="image">Изображение</label><br/>
        <input type="file" id="image" name="image"
            accept="image/.png,.jpg,.jpeg,.webp,.gif" /><br/>

        <p>Текст поста</p>
        <textarea name="text" rows="4" required></textarea>

        <span id="msg"><?= $msg ?></span>

        <div class="buttons">
            <input class="button-submit" type="submit" value="Отправить" />
        </div>
    </form>
</section>
