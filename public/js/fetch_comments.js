function fetch_comments(blog_id) {
    const comments_block = document.getElementById('comments');
    fetch(`/api/blog/${blog_id}/get_comments`)
        .then(res => {
            if (!res.ok) {
                throw new Error('Unab,e to fetch comments');
            }
            return res.json();
        }).then(data => {
            for (comment in data) {
                console.log();
            }
        }).catch(e => {
            console.error(e);
        });
}
