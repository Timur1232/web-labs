async function send_comment(blog_id) {
    try {
        const res = await fetch(`/api/blog/${blog_id}/add_comment`, {
            method: 'post',
            body: JSON.stringify({
                text: text.value
            }),
            headers: {
                "Content-Type": "application/json; charset=utf-8",
            },
        });
        if (!res.ok) {
            comment_msg.textContent = 'Что-то пошло не так... Попробуйте еще раз.';
            comment_msg.classList.remove('msg_good');
            comment_msg.classList.add('msg_error');
            if (res.status < 400) {
                const err = await res.json();
                console.log(err.error);
            } else {
                console.log(res)
            }
            return
        }

        comment_msg.textContent = 'Комментрарий успешно добавлен!';
        comment_msg.classList.add('msg_good');
        comment_msg.classList.remove('msg_error');

        const comment = await res.json();

        const node = document.createElement('div');
        node.innerHTML = `
            <h4>${comment.user_name}</h4>
            <p>UTC ${comment.date}</p>
            <p style="margin-bottom:15px; padding: 5px;">${comment.text}</p>`
        comments.insertBefore(node, comments.firstElementChild);

        const comment_tag = document.getElementById('comment');
        const button_html = await fetch(`/api/blog/${blog_id}/button`)
        if (!button_html.ok) {
            comment_msg.textContent = 'Что-то пошло не так... Попробуйте еще раз.';
            comment_msg.classList.remove('msg_good');
            comment_msg.classList.add('msg_error');
            if (button_html.status < 400) {
                const err = button_html.json()
                console.log(err);
            } else {
                console.log(button_html)
            }
            return
        }
        comment_tag.innerHTML = await button_html.text()
        htmx.process(comment_tag)

    } catch (e) {
        console.log(e);
        comment_msg.textContent = 'Что-то пошло не так... Попробуйте еще раз.';
        comment_msg.classList.remove('msg_good');
        comment_msg.classList.add('msg_error');
    }
}
