function createDropMenuItem() {
    return $('<li class="drop-menu-item"><a class="nav-link"></a></li>');
}

function appendDropMenuToElement(element, anchors) {
    const elementRect = element.getBoundingClientRect();
    const dropMenu = $('<ul>')
        .addClass('drop-menu')
        .css('position', 'fixed')
        .css('top', `${elementRect.top + elementRect.height}px`)
        .css('left', `${elementRect.left}px`);

    const itemTemplate = createDropMenuItem();
    for (let i = 0; i < anchors.length; i += 1) {
        const item = itemTemplate.clone(true);
        $(item).find('a')
            .attr('href', anchors[i].href)
            .text(anchors[i].text);
        $(item).appendTo(dropMenu);
    }
    dropMenu.appendTo(element);
}

function addDropMenuEventLiseners(element, anchors) {
    $(element).on('mouseenter', function() {
        appendDropMenuToElement(this, anchors);
    });

    $(element).on('mouseleave', function() {
        const dropMenu = $(this).find('.drop-menu');
        if (dropMenu != null) {
            dropMenu.remove();
        }
    });
}

$(document).ready(function() {
    const interestsLink = $('#interests-link');
    addDropMenuEventLiseners(interestsLink, [
        { href: '/my_interests#hobbies', text: 'Мои хобби' },
        { href: '/my_interests#games', text: 'Любимые игры' },
        { href: '/my_interests#music', text: 'Любимая музыка' },
    ]);

    const studesLink = $('#studies-link');
    addDropMenuEventLiseners(studesLink, [
        { href: '/studies/test', text: 'Тест' },
    ]);
});
