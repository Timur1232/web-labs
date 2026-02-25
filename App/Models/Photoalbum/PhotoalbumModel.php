<?php

namespace App\Models\Photoalbum;

final class PhotoalbumModel {
    /** @param array<PhotoItem> $photos */
    public function __construct(
        public array $photos,
    ) { }

    /** @param array<PhotoItem> $photos */
    public static function from(array $photos): self {
        return new self($photos);
    }

    public static function default(): self {
        return self::from([
            PhotoItem::from('photo1.jpg', 'Горящее пианино', 'Это Imagine Dragons?', 'Огненная музыка'),
            PhotoItem::from('photo2.jpg', 'Волынщик на моноколесе', 'HELL YEAH!!!', 'Эпичный волынщик'),
            PhotoItem::from('photo3.jpg', 'Гаст из майнкрафта с атомной бомбой', 'Надеюсь ничего не произойдет', 'Гаст с царь бимбой'),
            PhotoItem::from('photo4.jpg', 'Лягушка со скрипкой', 'Это среда чувакииииииии!', 'Лягушка'),
            PhotoItem::from('photo5.jpg', 'Круглый пруд', 'Везде обман! Торт это ЛОЖЬ!', 'Круглый пруд'),
            PhotoItem::from('photo6.jpg', 'Корова с лестницей на голове', 'Любопытной корове вымя оторвали', 'Любопытная корова'),
            PhotoItem::from('photo7.jpg', 'Мужик с крокодилом', 'Это могли бы быть мы с тобой, но ты не дед в очках', 'Мужик с крокодилом'),
            PhotoItem::from('photo8.jpg', 'Страшные костюмы', 'Я и мои последние две извилины', 'Ночной кошмар'),
            PhotoItem::from('photo9.jpg', 'ААААААААААААААА!!!', 'ААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААА', 'АААААААААААААА!!!!!!'),
            PhotoItem::from('photo10.jpg', 'Младенец с РПГ', 'Я не знаю что тут дополнять', 'Идеальный солдат'),
            PhotoItem::from('photo11.jpg', 'Круто!', 'Круто!', 'Круто!'),
            PhotoItem::from('photo12.jpg', 'Японец с огненным оружием', 'Эпично!', 'Огненная крутилка'),
            PhotoItem::from('photo13.jpg', 'Закрытая на ночь игровая площадка с супермаркете', 'Обернись', 'Когда остался один в супермаркете'),
            PhotoItem::from('photo14.jpg', 'Кот на скейтборде', 'Крутой!', 'Кот на скейтборде'),
            PhotoItem::from('photo15.jpg', 'Депресивный Гарфилд', 'Тупа я', 'Самый счастливый студент СевГУ'),
            PhotoItem::from('photo16.jpg', 'Собака в каске', 'Старое фото', 'Собака в каске'),
            PhotoItem::from('photo17.jpg', 'Бабка в костюме из травы', 'Фото холмов', 'Ну тут хз'),
            PhotoItem::from('photo18.jpg', 'Динозавры-лудоманы', 'Let\'s go gambling!', 'Дино-лудомания')
        ]);
    }
}
