function createAnchorList(...items) {
    const validItems = items.filter(item =>
        item && typeof item === "object" && "text" in item && "href" in item
    );

    if (validItems.length === 0) {
        alert("Нет предметов");
        return;
    }

    const t = document.createElement("template");
    const f = document.createDocumentFragment();

    const listItemsHtml = validItems.map(item =>
        `<li><a href="${item.id}">${item.text}</a></li>`
    ).join("");

    t.innerHTML = listItemsHtml;
    f.appendChild(t.content);

    const list = document.querySelector(".contents-list ol");
    list.appendChild(f);
}

createAnchorList(
    { text: "Мои хобби", href: "#hobbies" },
    { text: "Любимые игры", href: "#games" },
    { text: "Любимая музыка", href: "#music" }
);
