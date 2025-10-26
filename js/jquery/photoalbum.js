const photos = [
    { filename: 'photo1.jpg', alt: 'Горящее пианино', title: 'Это Imagine Dragons?', label: 'Огненная музыка' },
    { filename: 'photo2.jpg', alt: 'Волынщик на моноколесе', title: 'HELL YEAH!!!', label: 'Эпичный волынщик' },
    { filename: 'photo3.jpg', alt: 'Гаст из майнкрафта с атомной бомбой', title: 'Надеюсь ничего не произойдет', label: 'Гаст с царь бимбой' },
    { filename: 'photo4.jpg', alt: 'Лягушка со скрипкой', title: 'Это среда чувакииииииии!', label: 'Лягушка' },
    { filename: 'photo5.jpg', alt: 'Круглый пруд', title: 'Везде обман! Торт это ЛОЖЬ!', label: 'Круглый пруд' },
    { filename: 'photo6.jpg', alt: 'Корова с лестницей на голове', title: 'Любопытной корове вымя оторвали', label: 'Любопытная корова' },
    { filename: 'photo7.jpg', alt: 'Мужик с крокодилом', title: 'Это могли бы быть мы с тобой, но ты не крокодил в очках', label: 'Мужик с крокодилом' },
    { filename: 'photo8.jpg', alt: 'Страшные костюмы', title: 'Я и мои последние две извилины', label: 'Ночной кошмар' },
    { filename: 'photo9.jpg', alt: 'ААААААААААААААА!!!', title: 'ААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААА', label: 'АААААААААААААА!!!!!!' },
    { filename: 'photo10.jpg', alt: 'Младенец с РПГ', title: 'Я не знаю что тут дополнять', label: 'Идеальный солдат' },
    { filename: 'photo11.jpg', alt: 'Круто!', title: 'Круто!', label: 'Круто!' },
    { filename: 'photo12.jpg', alt: 'Японец с огненным оружием', title: 'Эпично!', label: 'Огненная крутилка' },
    { filename: 'photo13.jpg', alt: 'Закрытая на ночь игровая площадка с супермаркете', title: 'Обернись', label: 'Когда остался один в супермаркете' },
    { filename: 'photo14.jpg', alt: 'Кот на скейтборде', title: 'Крутой!', label: 'Кот на скейтборде' },
    { filename: 'photo15.jpg', alt: 'Депресивный Гарфилд', title: 'Тупа я', label: 'Самый счастливый студент СевГУ' },
    { filename: 'photo16.jpg', alt: 'Собака в каске', title: 'Старое фото', label: 'Собака в каске' },
    { filename: 'photo17.jpg', alt: 'Бабка в костюме из травы', title: 'Фото холмов', label: 'Ну тут хз' },
    { filename: 'photo18.jpg', alt: 'Динозавры-лудоманы', title: 'Let\'s go gambling!', label: 'Дино-лудомания' }
];

$(document).ready(function() {
    const cardTemplate = $('#photo-card-template');

    function createImageCard(photoSpecs) {
        const card = cardTemplate.contents().clone();
        const imageContainer = card.find('.photo-image-container');
        const labelContainer = card.find('.photo-label');

        $('<img>', {
            src: `/media/photo/${photoSpecs.filename}`,
            alt: photoSpecs.alt,
            title: photoSpecs.title,
        }).appendTo(imageContainer);

        $('<p>').text(photoSpecs.label).appendTo(labelContainer);

        return card;
    }

    const photoContainer = $('.photo-container');
    photos.forEach(p => {
        photoContainer.append(createImageCard(p));
    });

    const fullscreenDiv = $('#fullscreen-photo');
    let fullscreenOpen = false;
    function openFullscreen(src) {
        $('<img>', {
            src: src,
            class: 'rounded',
        }).appendTo(fullscreenDiv);
        fullscreenDiv.addClass('show');
        fullscreenOpen = true;
    }

    $('.photo-card').click(function() {
        const imgSrc = $(this).find('img').attr('src');
        openFullscreen(imgSrc);
    });

    fullscreenDiv.click(function (e) {
        if (fullscreenOpen && !$(e.target).is('img')) {
            $(this).find('img').remove();
            $(this).removeClass('show');
            fullscreenOpen = false;
        }
    });
});

