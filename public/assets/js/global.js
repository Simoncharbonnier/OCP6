document.addEventListener("DOMContentLoaded", () => {

    // Flash messages close
    document.querySelectorAll('.alert .close').forEach(function (close) {
        close.addEventListener('click', function () {
            this.closest('.alert').remove();
        })
    })

    // Pagination
    const pagination = document.querySelector('.pagination');
    if (pagination) {
        const items = pagination.querySelectorAll('.page-item');
        let counter = 1;
        items.forEach(function (item) {
            if (counter === 1) {
                item.querySelector('.page-link').innerHTML = "«";
            } else if (counter === items.length) {
                item.querySelector('.page-link').innerHTML = "»";
            }

            counter++;
        })
    }
});
