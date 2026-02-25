<?php
/** 
* @var string $title
* @var ?string $msg
*/ ?>
<div class="error-page">
    <article class="error-page-box">
        <h1 class="error-page-title"><?= $title ?> :(</h1>
        <?php if (isset($msg)): ?>
        <h2 class="error-page-sorry"><?= $msg ?></h2>
        <?php endif ?>
        <h2 class="error-page-sorry">sorry...</h2>
    </article>
    <a href="/">На главную</a>
</div>
