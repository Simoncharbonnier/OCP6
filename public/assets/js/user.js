document.addEventListener("DOMContentLoaded", () => {
    // Click
    function click(target) {
        const event = new MouseEvent("click");
        target.dispatchEvent(event);
    }

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

    const inputText = document.querySelector('.avatar-input-text input');
    const avatar = document.querySelector('.avatar');
    const deleteIcon = document.querySelector('.icon.delete-avatar');

    const inputFile = document.getElementById('avatar-input-file');
    if (inputFile) {

        avatar.addEventListener('click', function () {
            click(inputFile);
        })

        inputFile.addEventListener('change', function () {
            const file = inputFile.files[0];
            const reader = new FileReader();
            reader.readAsDataURL(file);

            reader.onloadend = function () {
                avatar.querySelector('img').src = reader.result;
                inputText.value = reader.result;
                deleteIcon.classList.remove('d-none');
            }
        })

        deleteIcon.addEventListener('click', function () {
            inputText.setAttribute('value', null);
            avatar.querySelector('img').src = 'https://dummyimage.com/50x50/ced4da/6c757d.jpg';
            deleteIcon.classList.add('d-none');
        })
    }
})
