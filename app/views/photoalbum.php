<section class="content-container" id="photoalbum">
    <h1 class="content-title">Мои любимые фотокарточки</h1>
    <template id="photo-card-template">
        <li class="photo-card shadow">
            <div class="photo-image-container"></div>
            <div class="photo-label"></div>
        </li>
    </template>
    <ul class="photo-container"></ul>
    <div id="fullscreen-photo">
        <p id="photo-label"></p>
        <div id="controls">
            <span id="prev-photo" class="arrow-button">←</span>
            <span id="photo-number"></span>
            <span id="next-photo" class="arrow-button">→</span>
        </div>
    </div>
</section>
