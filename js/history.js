function getCookies() {
    if (document.cookie === '') {
        return {
            pageNames: '',
            pageLinks: '',
            times: '',
        }
    }
    const cookies = {};
    document.cookie.split('; ').forEach(c => {
        const [key, value] = c.split('=');
        cookies[key] = value;
    });
    return cookies;
}

function getSessionHistoryFromCookies() {
    const cookies = getCookies();
    const pageNames = cookies.pageNames.split('&');
    const pageLinks = cookies.pageLinks.split('&');
    const times = cookies.times.split('&');

    if (pageNames[0] === ''
        || pageLinks[0] === ''
        || times[0] === '') {
        return [];
    }

    const history = [];
    for (let i = 0; i < pageNames.length; i += 1) {
        history.push({
            pageName: pageNames[i],
            pageLink: pageLinks[i],
            time: times[i],
        });
    }
    return history;
}

function setSessionHistoryInCookies(history) {
    let pageNames = history[0].pageName;
    let pageLinks = history[0].pageLink;
    let times = history[0].time;

    for (let i = 1; i < history.length; i += 1) {
        pageNames += '&' + history[i].pageName;
        pageLinks += '&' + history[i].pageLink;
        times += '&' + history[i].time;
    }

    document.cookie = `pageNames=${pageNames}`;
    document.cookie = `pageLinks=${pageLinks}`;
    document.cookie = `times=${times}`;
    document.cookie = `Max-Age=${60 * 60}`;
}

function getAllTimeHistoryFromLS() {
    const localStorageItem = localStorage.getItem('allTimeHistory');
    if (localStorageItem == null) {
        return [];
    }
    return JSON.parse(localStorageItem);
}

function setAllTimeHistoryInLS(history) {
    localStorage.setItem('allTimeHistory', JSON.stringify(history));
}

function formatDate(date) {
    const year = date.getFullYear().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const day = date.getDate().toString().padStart(2, '0');
    const hour = date.getHours().toString().padStart(2, '0');
    const minute = date.getMinutes().toString().padStart(2, '0');
    return `${day}.${month}.${year} ${hour}:${minute}`;
}

function max(a, b) {
    if (a >= b) {
        return a;
    } else {
        return b;
    }
}

function parseDate(dateStr) {
    const [date, time] = dateStr.split(' ');
    const [day, month, year] = date.split('.');
    const [hour, minute] = time.split(':');
    return new Date(year, month-1, day, hour, minute);
}

function displayHistory() {
    const allTimeHistory = getAllTimeHistoryFromLS();
    const sessionHistory = getSessionHistoryFromCookies();

    const historyTable = document.getElementById('history-table');
    let html = '';

    let maxLength = max(sessionHistory.length, allTimeHistory.length);
    for (let i = 0; i < maxLength; i += 1) {
        html += '<tr>'

        if (i < sessionHistory.length) {
            html += `<th style="text-align:left">${i + 1}. <a href="${sessionHistory[i].pageLink}">${sessionHistory[i].pageName}</a></th>
                     <th style="text-align:left">${formatDate(parseDate(sessionHistory[i].time))}</th>`;
        } else {
            html += '<th></th><th></th>';
        }

        if (i < allTimeHistory.length) {
            html += `<th style="text-align:left">${i + 1}. <a href="${allTimeHistory[i].pageLink}">${allTimeHistory[i].pageName}</a></th>
                     <th style="text-align:left">${formatDate(parseDate(allTimeHistory[i].time))}</th>`;
        } else {
            html += '<th></th><th></th>';
        }

        html += '</tr>'
    }

    historyTable.innerHTML = html;
}

function trackPage(pageName, pageLink) {
    const allTimeHistory = getAllTimeHistoryFromLS();
    const sessionHistory = getSessionHistoryFromCookies();
    let now = formatDate(new Date());

    if (sessionHistory.length >= 121) {
        for (let i = 0; i < sessionHistory.length - 1; i += 1) {
            sessionHistory[i] = sessionHistory[i + 1];
        }
        sessionHistory[120] = {
            pageName: pageName,
            pageLink: pageLink,
            time: now,
        };
    } else {
        sessionHistory.push({
            pageName: pageName,
            pageLink: pageLink,
            time: now,
        });
    }

    allTimeHistory.push({
        pageName: pageName,
        pageLink: pageLink,
        time: now,
    });

    setSessionHistoryInCookies(sessionHistory);
    setAllTimeHistoryInLS(allTimeHistory);
}

function resetHistory() {
    setSessionHistoryInCookies([{
        pageName: '',
        pageLink: '',
        time: '',
    }]);
    setAllTimeHistoryInLS([]);
    const historyTable = document.getElementById('history-table');
    historyTable.innerHTML = '';
}

document.addEventListener('DOMContentLoaded', _ => {
    const pathDecomposed = window.location.href.split('/');
    if (pathDecomposed[pathDecomposed.length - 1] === 'history') {
        return;
    }
    const href = '/' + pathDecomposed[pathDecomposed.length - 1];
    trackPage(document.title, href);
});
