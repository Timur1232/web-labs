<?php
use App\Models\Dto\Blog_Record;
/**
* @var string $msg
* @var ?Blog_Record $blog
*/
?>
<?php if ($blog == null): ?>
<section class="content-container">
<?php endif ?>
    <form id="test-form"
        enctype="multipart/form-data"
        <?php if ($blog != null): ?>
            action="/admin/blog/<?= $blog->id ?>/edit"
        <?php else: ?>
            action="/admin/blog/post"
        <?php endif ?>
        method="post"
    >
        <!-- hx-post="/admin/blog/post" hx-target="#msg" hx-swap="outerHTML" -->
        <label for="title">Заголовок:</label>
        <input class="input-text" type="text" id="title" name="title" required
            <?php if ($blog != null && $blog->title != null): ?>
            value="<?= $blog->title ?>"
            <?php endif ?>
        ></input>

        <label for="author">Автор:</label>
        <input class="input-text" type="text" id="author" name="author" required
            <?php if ($blog != null && $blog->author != null): ?>
            value="<?= $blog->author ?>"
            <?php endif ?>
        ></input>

        <label for="image">Изображение</label><br/>
        <input type="file" id="image" name="image"
            accept="image/.png,.jpg,.jpeg,.webp,.gif" /><br/>
        <?php if ($blog != null && $blog->image_path != null): ?>
        <p>Текущее изображение:</p>
        <img src="<?= $blog->image_path ?>" alt="image">
        <?php endif ?>

        <p>Текст поста</p>
        <textarea name="text" rows="4" required>
            <?php if ($blog != null && $blog->text != null): ?>
            <?= $blog->text ?>
            <?php endif ?>
        </textarea>

        <span id="msg"><?= $msg ?></span>

        <div class="buttons">
            <input class="button-submit" type="submit" value="Отправить" />
        </div>
    </form>
<?php if ($blog == null): ?>
</section>
<?php endif ?>
