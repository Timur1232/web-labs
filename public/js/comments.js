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
            const err = await res.json();
            console.log(err.error);
        } else {
            comment_msg.textContent = 'Комментрарий успешно добавлен!';

            const comment = await res.json();

            const node = document.createElement('div');
            node.innerHTML = `<h2>${comment.user_name}</h2><p>UTC ${comment.date}</p><p style="margin-bottom:15px; padding: 5px;">${comment.text}</p>`
            comments.insertBefore(node, comments.firstElementChild);

            const html_res = await fetch(`/api/blog/${blog_id}/button`, {
                method: "get"
            });
            if (!html_res.ok) {
                console.log('fu');
                return;
            }
            const html = await html_res.text();
            const comment_tag = document.getElementById('comment');
            comment_tag.innerHTML = html;
        }

    } catch (e) {
        console.log(e);
        comment_msg.textContent = 'Что-то пошло не так... Попробуйте еще раз.';
    }
}
