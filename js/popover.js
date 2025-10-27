let popover = 0;

function createPopover(elem, content, horizontal) {
    deletePopover();
    const div = $('<div>')
        .append($(content))
        .appendTo($('body'));

    if ($(elem).css('position') === 'fixed') {
        div.css('position', 'fixed');
    }

    const contentSize = {
        width: $(content).outerWidth(true),
        height: $(content).outerHeight(true),
    };

    let boundingRect = null;
    if ($(elem).css('position') === 'fixed') {
        boundingRect = $(elem).get(0).getBoundingClientRect();
    } else {
        boundingRect = $(elem).offset();
    }

    const elemRect = {
        width: $(elem).outerWidth(),
        height: $(elem).outerHeight(),
        top: boundingRect.top,
        left: boundingRect.left,
    };

    let popoverPos = {
        left: elemRect.left + elemRect.width / 2 - contentSize.width / 2,
        top: elemRect.top - contentSize.height + 50,
    };

    if (horizontal) {
        const elemCenter = {
            x: elemRect.left + elemRect.width / 2,
            y: elemRect.top + elemRect.height / 2,
        };
        popoverPos.top = elemCenter.y - contentSize.height;
        if (elemCenter.x < window.outerWidth / 2) {
            popoverPos.left = elemRect.left + elemRect.width + 30;
        } else {
            popoverPos.left = elemRect.left - 30 - contentSize.width;
        }
    }

    if (popoverPos.left < 30) {
        popoverPos.left = 30;
    } else if (popoverPos.left + contentSize.width + 30 > window.outerWidth) {
        popoverPos.left = window.outerWidth - contentSize.width - 30;
    }

    if (popoverPos.top < 50) {
        popoverPos.top = 50;
    } else if (popoverPos.top + contentSize.height + 50 > window.outerHeight) {
        popoverPos.top = window.outerHeight - contentSize.height - 50;
    }

    div.addClass('popover')
        .css('top', popoverPos.top)
        .css('left', popoverPos.left);
}

function deletePopover() {
    $('.popover').remove();
}

let globalTimer = null;

function restartDeleteTimer(popoverSpecs) {
    if (globalTimer != null) {
        clearTimeout(globalTimer);
    }
    globalTimer = setTimeout(() => {
        deletePopover();
        globalTimer = null;
    }, popoverSpecs.sec * 1000);
}

function addPopoverListener(popoverSpecs) {
    $(popoverSpecs.id).on('mouseover', function() {
        if ($('.popover').data('id') === popoverSpecs.id && globalTimer!= null) {
            restartDeleteTimer(popoverSpecs);
            return;
        } else if (globalTimer != null) {
            restartDeleteTimer(popoverSpecs);
        }
        createPopover(this, $(`<span>${popoverSpecs.text}</span>`), popoverSpecs.horizontal);
        $('.popover').data('id', popoverSpecs.id);
    }).on('mouseleave', function() {
        restartDeleteTimer(popoverSpecs);
    });
}

const popover1 = {
    id: '#test',
    sec: 5,
    horizontal: false,
    text: 'popover',
}
const popover2 = {
    id: '#test2',
    sec: 5,
    horizontal: true,
    text: 'popover left',
}
const popover3 = {
    id: '#test3',
    sec: 5,
    horizontal: true,
    text: 'popover rigth',
}

$(document).ready(function() {
    addPopoverListener(popover1);
    addPopoverListener(popover2);
    addPopoverListener(popover3);
});
