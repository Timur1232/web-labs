<section id="admin_guest_book_form" class="content-container">
    <form action="/admin/guest_book/append" method="post"
        hx-post="/admin/guest_book/append"
        hx-target="admin_guest_book_form"
        hx-swap="outerHTML"
    >
        <label for="messege">CSV файл сообщений</label>
        <input type="file" id="messege" name="messege" enctype="text/csv" accept="text/csv"/>
        <input type="submit" value="Добавить" formaction="/admin/guest_book/append" />
        <input type="submit" value="Перезаписать" onclick="return confirm('Вы уверены? Это перезапишет существующие данные.')" formaction="/admin/guest_book/override" />
    </form>
</section>
