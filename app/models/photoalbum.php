<?php

namespace App\Models;

final class PhotoItem {
    public function __construct(
        public string $filename,
        public string $alt,
        public string $title,
        public string $label,
    ) { }
}

final class Photoalbum {
    /** @var array<PhotoItem> $photos */
    public array $photos;

    public function __construct() {
        $this->photos = array(
            new PhotoItem('photo1.jpg', 'Горящее пианино', 'Это Imagine Dragons?', 'Огненная музыка'),
            new PhotoItem('photo2.jpg', 'Волынщик на моноколесе', 'HELL YEAH!!!', 'Эпичный волынщик'),
            new PhotoItem('photo3.jpg', 'Гаст из майнкрафта с атомной бомбой', 'Надеюсь ничего не произойдет', 'Гаст с царь бимбой'),
            new PhotoItem('photo4.jpg', 'Лягушка со скрипкой', 'Это среда чувакииииииии!', 'Лягушка'),
            new PhotoItem('photo5.jpg', 'Круглый пруд', 'Везде обман! Торт это ЛОЖЬ!', 'Круглый пруд'),
            new PhotoItem('photo6.jpg', 'Корова с лестницей на голове', 'Любопытной корове вымя оторвали', 'Любопытная корова'),
            new PhotoItem('photo7.jpg', 'Мужик с крокодилом', 'Это могли бы быть мы с тобой, но ты не крокодил в очках', 'Мужик с крокодилом'),
            new PhotoItem('photo8.jpg', 'Страшные костюмы', 'Я и мои последние две извилины', 'Ночной кошмар'),
            new PhotoItem('photo9.jpg', 'ААААААААААААААА!!!', 'ААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААА', 'АААААААААААААА!!!!!!'),
            new PhotoItem('photo10.jpg', 'Младенец с РПГ', 'Я не знаю что тут дополнять', 'Идеальный солдат'),
            new PhotoItem('photo11.jpg', 'Круто!', 'Круто!', 'Круто!'),
            new PhotoItem('photo12.jpg', 'Японец с огненным оружием', 'Эпично!', 'Огненная крутилка'),
            new PhotoItem('photo13.jpg', 'Закрытая на ночь игровая площадка с супермаркете', 'Обернись', 'Когда остался один в супермаркете'),
            new PhotoItem('photo14.jpg', 'Кот на скейтборде', 'Крутой!', 'Кот на скейтборде'),
            new PhotoItem('photo15.jpg', 'Депресивный Гарфилд', 'Тупа я', 'Самый счастливый студент СевГУ'),
            new PhotoItem('photo16.jpg', 'Собака в каске', 'Старое фото', 'Собака в каске'),
            new PhotoItem('photo17.jpg', 'Бабка в костюме из травы', 'Фото холмов', 'Ну тут хз'),
            new PhotoItem('photo18.jpg', 'Динозавры-лудоманы', 'Let\'s go gambling!', 'Дино-лудомания')
        );
    }
}
