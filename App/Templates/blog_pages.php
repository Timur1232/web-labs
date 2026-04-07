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
    <div class="pagination-list">
        <?php foreach ($posts as $post): ?>
        <article class="pagination-item">
            <div class="pagination-item-row <?= !empty($post->image_path) ? 'pagination-item-row-with-image' : 'pagination-item-row-without-image' ?>">
                <?php if (!empty($post->image_path)): ?>
                <div class="pagination-item-image">
                    <a href="/blog/<?= $post->id ?>?page=<?= $page ?>" class="pagination-item-title-link">
                        <img src="<?= $post->image_path ?>" alt="<?= $post->title ?>">
                    </a>
                </div>
                <?php endif; ?>
                <div class="pagination-item-header">
                    <h2 class="pagination-item-title">
                        <a href="/blog/<?= $post->id ?>?page=<?= $page ?>" class="pagination-item-title-link">
                            <?= $post->title ?>
                        </a>
                    </h2>
                    <div class="pagination-item-meta">
                        <span class="pagination-item-author">Автор: <?= $post->author ?></span>
                        <span class="pagination-item-date"> | UTC <?= $post->format() ?></span>
                    </div>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?= $controls->render() ?>
</section>
