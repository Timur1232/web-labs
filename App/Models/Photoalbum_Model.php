<?php namespace App\Models;
use App\Models\Dto\Photo_Item;

final class Photoalbum_Model {
    /** @param array<Photo_Item> $photos */
    public function __construct(
        public array $photos,
    ) { }

    /** @param array<Photo_Item> $photos */
    public static function from(array $photos): self {
        return new self($photos);
    }

    public static function default(): self {
        return self::from([
            Photo_Item::from('photo1.jpg', 'Горящее пианино', 'Это Imagine Dragons?', 'Огненная музыка'),
            Photo_Item::from('photo2.jpg', 'Волынщик на моноколесе', 'HELL YEAH!!!', 'Эпичный волынщик'),
            Photo_Item::from('photo3.jpg', 'Гаст из майнкрафта с атомной бомбой', 'Надеюсь ничего не произойдет', 'Гаст с царь бимбой'),
            Photo_Item::from('photo4.jpg', 'Лягушка со скрипкой', 'Это среда чувакииииииии!', 'Лягушка'),
            Photo_Item::from('photo5.jpg', 'Круглый пруд', 'Везде обман! Торт это ЛОЖЬ!', 'Круглый пруд'),
            Photo_Item::from('photo6.jpg', 'Корова с лестницей на голове', 'Любопытной корове вымя оторвали', 'Любопытная корова'),
            Photo_Item::from('photo7.jpg', 'Мужик с крокодилом', 'Это могли бы быть мы с тобой, но ты не дед в очках', 'Мужик с крокодилом'),
            Photo_Item::from('photo8.jpg', 'Страшные костюмы', 'Я и мои последние две извилины', 'Ночной кошмар'),
            Photo_Item::from('photo9.jpg', 'ААААААААААААААА!!!', 'ААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААА', 'АААААААААААААА!!!!!!'),
            Photo_Item::from('photo10.jpg', 'Младенец с РПГ', 'Я не знаю что тут дополнять', 'Идеальный солдат'),
            Photo_Item::from('photo11.jpg', 'Круто!', 'Круто!', 'Круто!'),
            Photo_Item::from('photo12.jpg', 'Японец с огненным оружием', 'Эпично!', 'Огненная крутилка'),
            Photo_Item::from('photo13.jpg', 'Закрытая на ночь игровая площадка с супермаркете', 'Обернись', 'Когда остался один в супермаркете'),
            Photo_Item::from('photo14.jpg', 'Кот на скейтборде', 'Крутой!', 'Кот на скейтборде'),
            Photo_Item::from('photo15.jpg', 'Депресивный Гарфилд', 'Тупа я', 'Самый счастливый студент СевГУ'),
            Photo_Item::from('photo16.jpg', 'Собака в каске', 'Старое фото', 'Собака в каске'),
            Photo_Item::from('photo17.jpg', 'Бабка в костюме из травы', 'Фото холмов', 'Ну тут хз'),
            Photo_Item::from('photo18.jpg', 'Динозавры-лудоманы', 'Let\'s go gambling!', 'Дино-лудомания')
        ]);
    }
}
