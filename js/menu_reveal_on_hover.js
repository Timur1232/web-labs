function createDropMenuItemTemplate() {
    const template = document.createElement('template');
    template.innerHTML = '<li class="drop-menu-item"><a class="nav-link"></a></li>';
    return template;
}

function appendDropMenuToElement(element, anchors) {
    const dropMenu = document.createElement('ul');
    const itemTemplate = createDropMenuItemTemplate();

    const elementRect = element.getBoundingClientRect();
    dropMenu.style.position = 'fixed';
    dropMenu.classList.add('drop-menu');
    dropMenu.style.top = `${elementRect.top + elementRect.height}px`;
    dropMenu.style.left = `${elementRect.left}px`;

    for (let i = 0; i < anchors.length; i += 1) {
        const item = document.importNode(itemTemplate.content, true);
        const a = item.querySelector('a');
        a.href = anchors[i].href;
        a.textContent = anchors[i].text;
        dropMenu.appendChild(item);
    }

    element.appendChild(dropMenu);

}

function addDropMenuEventLiseners(element, anchors) {
    element.addEventListener('mouseenter', (event) => {
        const link = event.currentTarget;
        appendDropMenuToElement(link, anchors);
    });

    element.addEventListener('mouseleave', (event) => {
        const link = event.currentTarget;
        const dropMenu = link.querySelector('.drop-menu');
        if (dropMenu != null) {
            link.removeChild(dropMenu);
        }
    });
}

const interestsLink = document.getElementById('interests-link');
addDropMenuEventLiseners(interestsLink, [
    { href: '/my_interests#hobbies', text: 'Мои хобби' },
    { href: '/my_interests#games', text: 'Любимые игры' },
    { href: '/my_interests#music', text: 'Любимая музыка' },
]);

const studesLink = document.getElementById('studies-link');
addDropMenuEventLiseners(studesLink, [
    { href: '/studies/test', text: 'Тест' },
]);
