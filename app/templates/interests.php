<?php 
use App\Models\InterestsModel;
/** @var InterestsModel $model */
?>
<section class="content-with-aside">
    <section class="content-container">
        <h1 class="content-title">Мои интересы</h1>
        <?php foreach ($model->sections as $section): ?>
            <hr>
            <section class="interests-section" id="<?= $section->id ?>">
                <h1><?= $section->title ?></h1>
                <?php foreach ($section->articles as $article): ?>
                    <article class="content-block">
                        <h2><?= $article->title ?></h2>
                        <p><?= $article->caption ?></p>
                        <?php if (count($article->images) > 1): ?>
                            <div class="interest-image-gallery">
                        <?php endif ?>
                        <?php foreach ($article->images as $img): ?>
                            <?= $img->render() ?>
                        <?php endforeach ?>
                        <?php if (count($article->images) > 1): ?>
                            </div>
                        <?php endif ?>
                    </article>
                <?php endforeach ?>
            </section>
        <?php endforeach ?>
<!--
        <section class="interests-section" id="hobbies">
            <h1>Мои хобби</h1>
            <article class="content-block">
                <h2>Программирование</h2>
                <p>Люблю программировать программы.</p>
                <img class="interest-image-single rounded shadow" src="/public/media/interests/hackerman.jpg"
                    alt="Hackerman" title="Hackerman" />
            </article>
        </section>
        <hr>
        <section class="interests-section" id="games">
            <h1>Любимые игры</h1>
            <article class="content-block">
                <h2>Minecraft</h2>
                <p>Моя первая игра.</p>
                <img class="interest-image-single rounded shadow" src="/public/media/interests/minecraft.jpg"
                    alt="Minecraft" title="Minecraft" />
            </article>
            <article class="content-block">
                <h2>Factorio</h2>
                <p>Завод должен расти!</p>
                <img class="interest-image-single rounded shadow" src="/public/media/interests/factorio.jpg"
                    alt="Factorio" title="Factorio" />
            </article>
            <article class="content-block">
                <h2>Dishonored</h2>
                <p>Любимый стелс-экшен.</p>
                <img class="interest-image-single rounded shadow" src="/public/media/interests/dishonored.jpeg"
                    alt="Dishonored" title="Dishonored" />
            </article>
            <article class="content-block">
                <h2>Hollow Knight</h2>
                <p>Silksong реален!</p>
                <img class="interest-image-single rounded shadow" src="/public/media/interests/hollow-knight.webp"
                    alt="Hollow Knight" title="Hollow Knight" />
            </article>
            <article class="content-block">
                <h2>Far Cry 3</h2>
                <p>Моя любимая часть серии.</p>
                <img class="interest-image-single rounded shadow" src="/public/media/interests/far-cry-3.jpg"
                    alt="Far Cry 3" title="Far Cry 3" />
            </article>
        </section>
        <hr>
        <section class="interests-section" id="music">
            <h1>Любимая музыка</h1>
            <article class="content-block">
                <h2>System of A Down</h2>
                <p>Обожаю сочетание металла с лирическими моментами.</p>
                <div class="interest-image-gallery">
                    <img class="interest-image rounded shadow" src="/public/media/interests/soad1.jpg"
                        alt="System of A Down" title="System of A Down" />
                    <img class="interest-image rounded shadow" src="/public/media/interests/soad2.jpg"
                        alt="System of A Down" title="System of A Down" />
                    <img class="interest-image rounded shadow" src="/public/media/interests/soad3.jpg"
                        alt="System of A Down" title="System of A Down" />
                </div>
            </article>
            <article class="content-block">
                <h2>Radiohead</h2>
                <p>Душевная меланхоличная музыка альтернативного рока. Слушаю каждый альбом.</p>
                <div class="interest-image-gallery">
                    <img class="interest-image rounded shadow" src="/public/media/interests/radiohead1.jpg"
                        alt="Radiohead" title="Radiohead" />
                    <img class="interest-image rounded shadow" src="/public/media/interests/radiohead2.jpg"
                        alt="Radiohead" title="Radiohead" />
                    <img class="interest-image rounded shadow" src="/public/media/interests/radiohead3.png"
                        alt="Radiohead" title="Radiohead" />
                </div>
            </article>
            <article class="content-block">
                <h2>Slipknot</h2>
                <p>Слушаю только первые три альбома, но все равно одна из моих любимых тяжелых групп.</p>
                <div class="interest-image-gallery">
                    <img class="interest-image rounded shadow" src="/public/media/interests/slipknot1.jpg"
                        alt="Slipknot" title="Slipknot" />
                    <img class="interest-image rounded shadow" src="/public/media/interests/slipknot2.jpg"
                        alt="Slipknot" title="Slipknot" />
                    <img class="interest-image rounded shadow" src="/public/media/interests/slipknot3.jpg"
                        alt="Slipknot" title="Slipknot" />
                </div>
            </article>
            <article class="content-block">
                <h2>Linkin Park</h2>
                <p>Обожаю их сочетание хард-рока с хип-хопом.</p>
                <div class="interest-image-gallery">
                    <img class="interest-image rounded shadow" src="/public/media/interests/linkin-park1.jpg"
                        alt="Linkin Park" title="Linkin Park" />
                    <img class="interest-image rounded shadow" src="/public/media/interests/linkin-park2.jpg"
                        alt="Linkin Park" title="Linkin Park" />
                    <img class="interest-image rounded shadow" src="/public/media/interests/linkin-park3.jpg"
                        alt="Linkin Park" title="Linkin Park" />
                </div>
            </article>
            <article class="content-block">
                <h2>Pink Floyd</h2>
                <p>Очень хорошая музыка.</p>
                <div class="interest-image-gallery">
                    <img class="interest-image rounded shadow" src="/public/media/interests/pink-floyd1.jpg"
                        alt="Pink Floyd" title="Pink Floyd" />
                    <img class="interest-image rounded shadow" src="/public/media/interests/pink-floyd2.png"
                        alt="Pink Floyd" title="Pink Floyd" />
                    <img class="interest-image rounded shadow" src="/public/media/interests/pink-floyd3.jpg"
                        alt="Pink Floyd" title="Pink Floyd" />
                </div>
            </article>
        </section>-->
    </section>
    <aside class="contents-list-box shadow">
        <div class="contents-list">
            <h4>Содержание</h4>
            <ol>
            </ol>
        </div>
    </aside>
</section>
