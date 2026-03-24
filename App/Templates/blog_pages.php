<?php
use App\Core\View\View;


use App\Models\BlogRecord;
/**
 * @var array<BlogRecord> $posts
 * @var int $page
 * @var int $page_count
 */

$controls = View::func(function () use ($page, $page_count) {
    ob_start();
    ?>
    <div class="pagination">
        <?php if ($page_count > 1 && $page > 0): ?>
        <a href="/blog/all/0" class="pagination-link">← Первая</a>
        <?php else: ?>
        <span class="pagination-link pagination-link-disabled">← Первая</span>
        <?php endif; ?>

        <?php if ($page > 0): ?>
        <a href="/blog/all/<?= $page - 1 ?>" class="pagination-link">← Предыдущая</a>
        <?php else: ?>
        <span class="pagination-link pagination-link-disabled">← Предыдущая</span>
        <?php endif; ?>

        <span><?= $page+1 ?>/<?= $page_count ?></span>

        <?php if ($page < $page_count - 1): ?>
        <a href="/blog/all/<?= $page + 1 ?>" class="pagination-link">Следующая →</a>
        <?php else: ?>
        <span class="pagination-link pagination-link-disabled">Следующая →</span>
        <?php endif; ?>

        <?php if ($page_count > 1 && $page < $page_count-1): ?>
        <a href="/blog/all/<?= $page_count - 1 ?>" class="pagination-link">Последняя →</a>
        <?php else: ?>
        <span class="pagination-link pagination-link-disabled">Последняя →</span>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
})
?>
<section class="content-container">
    <?= $controls->render() ?>
    <div class="blog-list">
        <?php foreach ($posts as $post): ?>
        <article class="blog-list-item">
            <div class="blog-list-row <?= !empty($post->image_path) ? 'blog-list-row-with-image' : 'blog-list-row-without-image' ?>">
                <?php if (!empty($post->image_path)): ?>
                <div class="blog-list-image">
                    <a href="/blog/<?= $post->id ?>?page=<?= $page ?>" class="blog-list-title-link">
                        <img src="<?= $post->image_path ?>" alt="<?= $post->title ?>">
                    </a>
                </div>
                <?php endif; ?>
                <div class="blog-list-header">
                    <h2 class="blog-list-title">
                        <a href="/blog/<?= $post->id ?>?page=<?= $page ?>" class="blog-list-title-link">
                            <?= $post->title ?>
                        </a>
                    </h2>
                    <div class="blog-list-meta">
                        <span class="blog-list-author">Автор: <?= $post->author ?></span>
                        <span class="blog-list-date"> | UTC <?= $post->format() ?></span>
                    </div>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?= $controls->render() ?>
</section>
