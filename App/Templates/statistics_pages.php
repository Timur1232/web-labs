<?php
use App\Models\Dto\Statistic;
use App\Core\View\View;
/**
 * @var array<Statistic> $stats
 * @var int $page
 * @var int $page_count
 */

$controls = View::func(function () use ($page, $page_count) {
    ob_start();
    ?>
    <div class="pagination">
        <?php if ($page_count > 1 && $page > 0): ?>
        <a href="/admin/stats/all/0" class="pagination-link">← Первая</a>
        <?php else: ?>
        <span class="pagination-link pagination-link-disabled">← Первая</span>
        <?php endif; ?>

        <?php if ($page > 0): ?>
        <a href="/admin/stats/all/<?= $page - 1 ?>" class="pagination-link">← Предыдущая</a>
        <?php else: ?>
        <span class="pagination-link pagination-link-disabled">← Предыдущая</span>
        <?php endif; ?>

        <span><?= $page+1 ?>/<?= $page_count ?></span>

        <?php if ($page < $page_count - 1): ?>
        <a href="/admin/stats/all/<?= $page + 1 ?>" class="pagination-link">Следующая →</a>
        <?php else: ?>
        <span class="pagination-link pagination-link-disabled">Следующая →</span>
        <?php endif; ?>

        <?php if ($page_count > 1 && $page < $page_count-1): ?>
        <a href="/admin/stats/all/<?= $page_count - 1 ?>" class="pagination-link">Последняя →</a>
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
        <?php foreach ($stats as $stat): ?>
        <article class="pagination-item">
            <div class="pagination-item-row pagination-item-row-without-image">
                <div class="pagination-item-header">
                    <h3>UTC <?= $stat->format() ?></h3>
                    <h3>Посещенная страница: <?= $stat->web_page ?></h3>
                    <p>IP адресс: <?= $stat->ip_address ?></p>
                    <p>Имя комрьютера: <?= $stat->host_name ?></p>
                    <p>Браузер: <?= $stat->browser_name ?></p>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?= $controls->render() ?>
</section>
