<?php
use App\Views\PhotoalbumView;
/*
* @var PhotoalbumModel $model
*/
?>
<section class="content-container" id="photoalbum">
    <h1 class="content-title">Мои любимые фотокарточки</h1>
    <ul class="photo-container">
        <?= PhotoalbumView::render_imgs($model) ?>
    </ul>
    <div id="fullscreen-photo">
        <p id="photo-label"></p>
        <div id="controls">
            <span id="prev-photo" class="arrow-button">←</span>
            <span id="photo-number"></span>
            <span id="next-photo" class="arrow-button">→</span>
        </div>
    </div>
</section>
