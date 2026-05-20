const comment_form = document.getElementById('comment_form');
const show_btn = document.getElementById('show_btn');
const comment_msg = document.getElementById('comment_msg');
let show_form = false;

function switch_comment_form_visibility() {
    if (show_form) {
        comment_form.classList.add('hidden');
        show_btn.classList.remove('hidden');
    } else {
        comment_form.classList.remove('hidden');
        show_btn.classList.add('hidden');
    }
    show_form = !show_form;
    comment_msg.textContent = '';
}

async function send_comment(blog_id) {
    try {
        const res = await fetch(`/blog/add_comment/${blog_id}`, {
            method: 'post',
            body: JSON.stringify({aboba: 'urmom'}) 
        });
        if (!res.ok) {
            switch_comment_form_visibility();
            comment_msg.textContent = 'Что-то пошло не так... Попробуйте еще раз.';
        }
    } catch (e) {
        console.log(e);
        switch_comment_form_visibility();
        comment_msg.textContent = 'Что-то пошло не так... Попробуйте еще раз.';
    }
}
