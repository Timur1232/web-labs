<?php

use App\Models\Photoalbum_Model;
use App\Views\Photoalbum_View;
/**
* @var Photoalbum_Model $model
*/
?>
<section class="content-container" id="photoalbum">
    <h1 class="content-title">Мои любимые фотокарточки</h1>
    <ul class="photo-container">
        <?= Photoalbum_View::render_imgs($model) ?>
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
