document.addEventListener("DOMContentLoaded", () => {

    // Flash messages close
    document.querySelectorAll('.alert .close').forEach(function (close) {
        close.addEventListener('click', function () {
            this.closest('.alert').remove();
        })
    })
});
