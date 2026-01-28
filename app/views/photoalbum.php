<?php
use App\Models\Photoalbum;
/** @var Photoalbum $photos */ ?>
<section class="content-container" id="photoalbum">
    <h1 class="content-title">Мои любимые фотокарточки</h1>
    <ul class="photo-container">
        <?php foreach ($photos->photos as $photo): ?>
        <li class="photo-card shadow">
            <div class="photo-image-container">
                <img src="/public/media/photo/<?= $photo->filename ?>" alt="<?= $photo->alt ?>" title="<?= $photo->title ?>" />
            </div>
            <div class="photo-label">
                <p><?= $photo->label ?></p>
            </div>
        </li>
        <?php endforeach; ?>
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
