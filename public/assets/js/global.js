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

    // Arrow down
    function getHeightScroll(el) {
        const rect = el.getBoundingClientRect();
        return rect.top + window.scrollY;
    }
    const arrowDown = document.querySelector('.banner .down');
    if (arrowDown) {
        arrowDown.addEventListener('click', function () {
            const row = document.querySelector('.row-tricks');
            window.scroll({ top: getHeightScroll(row) - 50, behavior: 'smooth' });
        })
    }
});
