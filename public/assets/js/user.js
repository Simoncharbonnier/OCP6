document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('.user-menu .item').forEach(function (item) {
        item.addEventListener('click', function () {
            if (!item.classList.contains('active')) {
                document.getElementById('tricks').classList.toggle('d-none');
                document.getElementById('comments').classList.toggle('d-none');
                document.querySelectorAll('.user-menu .item').forEach(function (item) {
                    item.classList.toggle('active');
                })
            }
        })
    })
})
