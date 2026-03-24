<?php
use App\Models\Test\BlogRecord;
/*
* @var BlogRecord $post
* @var int $page
*/
$has_image = !empty($post->image_path);
?>
<section class="content-container">
    <article class="blog-post">
        <a class="pagination-link" href="/blog/all/<?= $page ?? 0 ?>">Назад</a>
        <br/><br/><br/>
        <div class="blog-post-row <?= $has_image ? 'blog-post-row-with-image' : 'blog-post-row-without-image' ?>">
            <?php if ($has_image): ?>
                <div class="blog-post-image">
                    <img src="<?= $post->image_path ?>" alt="<?= $post->title ?>">
                </div>
            <?php endif; ?>
            <div class="blog-post-header">
                <h1 class="blog-post-title"><?= $post->title ?></h1>
                <div class="blog-post-meta">
                    <span class="blog-post-author">Автор: <?= $post->author ?></span>
                    <span class="blog-post-date"> | <?= $post->format() ?></span>
                </div>
            </div>
        </div>
        <hr/>
        <div class="blog-post-text">
            <?= $post->text ?>
        </div>
    </article>
</section>
