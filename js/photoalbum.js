const photos = [
    { filename: "photo1.jpg", alt: "Горящее пианино", title: "HELL YEAH!!!", label: "Огненная музыка" },
    { filename: "photo2.jpg", alt: "Волынщик на моноколесе", title: "Надеюсь ничего не произойдет", label: "Эпичный волынщик" },
    { filename: "photo3.jpg", alt: "Гаст из майнкрафта с атомной бомбой", title: "Это среда чувакииииииии!", label: "Гаст с царь бимбой" },
    { filename: "photo4.jpg", alt: "Лягушка со скрипкой", title: "Везде обман! Торт это ЛОЖЬ!", label: "Лягушка" },
    { filename: "photo5.jpg", alt: "Круглый пруд", title: "Любопытной корове вымя оторвали", label: "Круглый пруд" },
    { filename: "photo6.jpg", alt: "Корова с лестницей на голове", title: "Это могли бы быть мы с тобой, но ты не крокодил в очках", label: "Любопытная корова" },
    { filename: "photo7.jpg", alt: "Мужик с крокодилом", title: "Я и мои последние две извилины", label: "Мужик с крокодилом" },
    { filename: "photo8.jpg", alt: "Страшные костюмы", title: "ААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААА", label: "Ночной кошмар" },
    { filename: "photo9.jpg", alt: "ААААААААААААААА!!!", title: "Я не знаю что тут дополнять", label: "АААААААААААААА!!!!!!" },
    { filename: "photo10.jpg", alt: "Младенец с РПГ", title: "Круто!", label: "Идеальный солдат" },
    { filename: "photo11.jpg", alt: "Круто!", title: "Эпично!", label: "Круто!" },
    { filename: "photo12.jpg", alt: "Японец с огненным оружием", title: "Обернись", label: "Огненная крутилка" },
    { filename: "photo13.jpg", alt: "Закрытая на ночь игровая площадка с супермаркете", title: "Крутой!", label: "Когда остался один в супермаркете" },
    { filename: "photo14.jpg", alt: "Кот на скейтборде", title: "Тупа я", label: "Кот на скейтборде" },
    { filename: "photo15.jpg", alt: "Депресивный Гарфилд", title: "Старое фото", label: "Самый счастливый студент СевГУ" },
    { filename: "photo16.jpg", alt: "Собака в каске", title: "Фото холмов", label: "Собака в каске" },
    { filename: "photo17.jpg", alt: "Бабка в костюме из травы", title: "Let's go gambling!", label: "Ну тут хз" },
    { filename: "photo18.jpg", alt: "Динозавры-лудоманы", title: "UTF-8", label: "Дино-лудомания" }
];

const photoAlbum = document.querySelector(".photo-container");
const fragment = document.createDocumentFragment();
const template = document.querySelector("#photo-card-template");

for (let i = 0; i < photos.length; i += 1) {
    const { filename, alt, title, label } = photos[i];
    const card = document.importNode(template.content, true);

    const imageContainer = card.querySelector(".photo-image-container");
    const img = document.createElement("img");
    img.src = `/media/photo/${filename}`;
    img.alt = alt;
    img.title = title;
    imageContainer.appendChild(img);

    const labelContainer = card.querySelector(".photo-label");
    const p = document.createElement("p");
    p.textContent = label;
    labelContainer.appendChild(p);

    fragment.appendChild(card);
}

photoAlbum.appendChild(fragment);
