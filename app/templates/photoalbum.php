<?php
use App\Views\PhotoalbumView;
require_once 'app/views/photoalbum.php';
/*
* @var PhotoalbumView $view
* @var PhotoalbumModel $model
*/
?>
<section class="content-container" id="photoalbum">
    <h1 class="content-title">Мои любимые фотокарточки</h1>
    <ul class="photo-container">
        <?= $view->render_imgs($model) ?>
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
