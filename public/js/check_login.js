function check_login_callback() {
    const login_input = document.querySelector('input#login');
    const req = new XMLHttpRequest();
    req.open('POST', '/api/check_login', true);
    req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    req.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE) {
            if (this.status === 200) {
                const error = document.querySelector('.error');
                if (this.responseText !== 'true') {
                    error.innerHTML = 'Логин уже занят.';
                } else {
                    error.innerHTML = '';
                }
            } else {
                console.log('Server returned non OK status:', this.statusText);
            }
        }
    };
    req.send(`login=${login_input.value}`);
}
