function createAnchorList(...items) {
    const validItems = items.filter(item =>
        item && typeof item === 'object' && 'text' in item && 'href' in item
    );

    if (validItems.length === 0) {
        alert('Нет предметов');
        return;
    }

    const listItems = validItems.map(item =>
        $(`<li><a href='${item.href}'>${item.text}</a></li>`)
    );

    $('.contents-list ol').append(listItems);
}

$(document).ready(function() {
    createAnchorList(
        { text: 'Мои хобби', href: '#hobbies' },
        { text: 'Любимые игры', href: '#games' },
        { text: 'Любимая музыка', href: '#music' }
    );
});
