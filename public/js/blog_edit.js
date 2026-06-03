function get_iframe_document(iframe) {
    if (iframe.contentDocument) return iframe.contentDocument
    if (iframe.contentWindow)   return iframe.contentWindow.document
    return iframe.document
}

function edit_form(blog_id) {
    const blog_iframe = document.getElementById('blog-iframe');
    blog_iframe.src = `/admin/blog/${blog_id}/edit`;
    blog_iframe.onload = function() {
        const cont = document.getElementById('blog-content-container')
        cont.innerHTML = get_iframe_document(this).body.innerHTML
    }
}
