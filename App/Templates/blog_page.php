<?php
/*
* @var BlogRecord $post
* @var CommentRecord[] $comments
* @var int $page
* @var ?User $user
*/

use App\Views\BlogView;

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
                    <span class="blog-post-date"> | UTC <?= $post->format() ?></span>
                </div>
            </div>
        </div>
        <hr/>
        <div class="blog-post-text">
            <?= $post->text ?>
        </div>
    </article>

    <h2 style="margin-bottom:25px;">Комментарии</h2>
    <div id="comment">
        <?php if (!is_null($user)): ?>
            <?= BlogView::comment_button($post->id)->render() ?>
        <?php else: ?>
            <p>Войдите, чтобы оставлять комментарии.</p>
        <?php endif ?>
    </div>
    <hr/>
    <?php if (count($comments) !== 0): ?>
        <div id="comments">
        <?php foreach ($comments as $comment): ?>
            <h4><?= $comment->user_name ?></h4>
            <p><?= $comment->format() ?></p>
            <p style="margin-bottom:15px; padding: 5px;"><?= $comment->text ?></p>
        <?php endforeach ?>
        </div>
    <?php else: ?>
        <p style="margin-bottom:25px;">Комментариев пока нет.</p>
    <?php endif ?>
</section>
