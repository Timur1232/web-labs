<?php

namespace App\Models\Instances;

require_once 'app/models/interests.php';

use App\Models\{ InterestsModel, function article, function section };
use function App\Core\Helpers\img;

// single image default classes
const sc = ['interest-image-single', 'rounded', 'shadow'];
// multiple image default classes (for music)
const mc = ['interest-image', 'rounded', 'shadow'];

$model = new InterestsModel([
    section('hobbies', 'Мои хобби', [
        article('Программирование', 'Люблю программировать программы.', [
            img(
                src: '/public/media/interests/hackerman.jpg',
                alt: 'Hackerman',
                title: 'Hackerman',
                classes: sc,
            ),
        ]),
    ]),
    section('games','Любимые игры', [
        article('Minecraft', 'Моя первая игра.', [
            img(
                src: '/public/media/interests/minecraft.jpg',
                alt: 'Minecraft',
                title: 'Minecraft',
                classes: sc,
            ),
        ]),
        article('Factorio', 'Завод должен расти!', [
            img(
                src: '/public/media/interests/factorio.jpg',
                alt: 'Factorio',
                title: 'Factorio',
                classes: sc,
            ),
        ]),
        article('Dishonored', 'Любимый стелс-экшен.', [
            img(
                src: '/public/media/interests/dishonored.jpeg',
                alt: 'Dishonored',
                title: 'Dishonored',
                classes: sc,
            ),
        ]),
        article('Hollow Knight', 'Silksong реален!', [
            img(
                src: '/public/media/interests/hollow-knight.webp',
                alt: 'Hollow Knight',
                title: 'Hollow Knight',
                classes: sc,
            ),
        ]),
        article('Far Cry 3', 'Моя любимая часть серии.', [
            img(
                src: '/public/media/interests/far-cry-3.jpg',
                alt: 'Far Cry 3',
                title: 'Far Cry 3',
                classes: sc,
            ),
        ]),
    ]),
    section('music', 'Любимая музыка', [
        article('System of A Down', 'Обожаю сочетание металла с лирическими моментами.', [
            img(
                src: '/public/media/interests/soad1.jpg',
                alt: 'System of A Down',
                title: 'System of A Down',
                classes: mc,
            ),
            img(
                src: '/public/media/interests/soad2.jpg',
                alt: 'System of A Down',
                title: 'System of A Down',
                classes: mc,
            ),
            img(
                src: '/public/media/interests/soad3.jpg',
                alt: 'System of A Down',
                title: 'System of A Down',
                classes: mc,
            ),
        ]),
        article('Radiohead', 'Душевная меланхоличная музыка альтернативного рока. Слушаю каждый альбом.', [
            img(
                src: '/public/media/interests/radiohead1.jpg',
                alt: 'Radiohead',
                title: 'Radiohead',
                classes: mc,
            ),
            img(
                src: '/public/media/interests/radiohead2.jpg',
                alt: 'Radiohead',
                title: 'Radiohead',
                classes: mc,
            ),
            img(
                src: '/public/media/interests/radiohead3.png',
                alt: 'Radiohead',
                title: 'Radiohead',
                classes: mc,
            ),
        ]),
        article('Slipknot', 'Слушаю только первые три альбома, но все равно одна из моих любимых тяжелых групп.', [
            img(
                src: '/public/media/interests/slipknot1.jpg',
                alt: 'Slipknot',
                title: 'Slipknot',
                classes: mc,
            ),
            img(
                src: '/public/media/interests/slipknot2.jpg',
                alt: 'Slipknot',
                title: 'Slipknot',
                classes: mc,
            ),
            img(
                src: '/public/media/interests/slipknot3.jpg',
                alt: 'Slipknot',
                title: 'Slipknot',
                classes: mc,
            ),
        ]),
        article('Linkin Park', 'Обожаю их сочетание хард-рока с хип-хопом.', [
            img(
                src: '/public/media/interests/linkin-park1.jpg',
                alt: 'Linkin Park',
                title: 'Linkin Park',
                classes: mc,
            ),
            img(
                src: '/public/media/interests/linkin-park2.jpg',
                alt: 'Linkin Park',
                title: 'Linkin Park',
                classes: mc,
            ),
            img(
                src: '/public/media/interests/linkin-park3.jpg',
                alt: 'Linkin Park',
                title: 'Linkin Park',
                classes: mc,
            ),
        ]),
        article('Pink Floyd', 'Очень хорошая музыка.', [
            img(
                src: '/public/media/interests/pink-floyd1.jpg',
                alt: 'Pink Floyd',
                title: 'Pink Floyd',
                classes: mc,
            ),
            img(
                src: '/public/media/interests/pink-floyd2.png',
                alt: 'Pink Floyd',
                title: 'Pink Floyd',
                classes: mc,
            ),
            img(
                src: '/public/media/interests/pink-floyd3.jpg',
                alt: 'Pink Floyd',
                title: 'Pink Floyd',
                classes: mc,
            ),
        ]),
    ]),
]);
return $model;
