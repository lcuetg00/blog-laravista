function changePageMode(value) {
    console.log(value);
    
    let body = document.getElementById('body');

    if(value === 1) {
        body.setAttribute('data-bs-theme', 'light');
    }

    if(value === 2) {
        body.setAttribute('data-bs-theme', 'dark');
    }
}