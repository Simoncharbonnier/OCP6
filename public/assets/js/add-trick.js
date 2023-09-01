document.addEventListener("DOMContentLoaded", () => {
    // Click
    function click(target) {
        const event = new MouseEvent("click");
        target.dispatchEvent(event);
    }

    // Add images
    function addImage(n) {
        const container = document.createElement('div');
        container.classList.add(`container-image-${n}`, 'd-flex', 'align-items-center');
        imagesContainer.appendChild(container);

        const inputContainer = document.createElement('div');
        inputContainer.innerHTML = imagesContainer.getAttribute('data-prototype').replace(/__name__/g, n);
        inputContainer.classList.add('d-none');
        container.appendChild(inputContainer);

        const input = inputContainer.querySelector('input');
        input.addEventListener('change', function () {
            const file = input.files[0];
            const reader = new FileReader();
            reader.readAsDataURL(file);

            reader.onloadend = function () {
                const wrapperImage = document.createElement('div');
                wrapperImage.classList.add('wrapper', 'd-flex', 'align-items-center', 'my-3');
                wrapperImage.style.width = '16rem';
                wrapperImage.style.height = '145px';

                const image = document.createElement('img');
                image.src = reader.result;
                wrapperImage.appendChild(image);

                container.appendChild(wrapperImage);

                const deleteIcon = document.createElement('img');
                deleteIcon.classList.add('icon');
                deleteIcon.src = '/assets/img/icons/close.svg';
                deleteIcon.setAttribute('data-image', n);
                deleteIcon.addEventListener('click', function () {
                    const imageNumber = this.getAttribute('data-image');
                    document.querySelector(`.container-image-${imageNumber}`).remove();
                })
                container.appendChild(deleteIcon);
            }
        });

        click(input);
    }

    document.getElementById('submitButton').addEventListener('click', function (e) {
        e.preventDefault;
        console.log(document.getElementById('trick_form_images_0_name').files);
    })

    const imagesContainer = document.getElementById('trick_form_images');

    let imageNumber = 0;
    document.getElementById('add-image').addEventListener('click', function () {
        addImage(imageNumber);
        imageNumber++;
    })

    // Add videos
    const videosContainer = document.getElementById('trick_form_videos');
    const addVideoBtn = document.getElementById('add_video');
    const inputVideo = document.getElementById('video-input');

    function addVideo(n, value) {
        let valueMatched = value.match(/watch\?v=([^&]*)/);
        if (valueMatched) {
            valueMatched = valueMatched[1];

            const container = document.createElement('div');
            container.classList.add(`container-video-${n}`, 'd-flex', 'align-items-center')
            videosContainer.appendChild(container);

            const inputContainer = document.createElement('div');
            inputContainer.innerHTML = videosContainer.getAttribute('data-prototype').replace(/__name__/g, n);
            inputContainer.querySelector('input').setAttribute('value', valueMatched);
            inputContainer.classList.add('d-none');
            container.appendChild(inputContainer);

            const wrapperVideo = document.createElement('div');
            wrapperVideo.classList.add('wrapper', 'd-flex', 'align-items-center', 'my-3');
            wrapperVideo.style.width = '16rem';
            wrapperVideo.style.height = '145px';

            const video = document.createElement('iframe');
            video.src = "https://www.youtube.com/embed/" + valueMatched;
            video.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
            video.setAttribute('allowfullscreen', true);
            video.setAttribute('frameborder', 0);
            wrapperVideo.appendChild(video);
            container.appendChild(wrapperVideo);

            const deleteIcon = document.createElement('img');
            deleteIcon.classList.add('icon');
            deleteIcon.src = '/assets/img/icons/close.svg';
            deleteIcon.setAttribute('data-video', n);
            deleteIcon.addEventListener('click', function () {
                const videoNumber = this.getAttribute('data-video');
                document.querySelector(`.container-video-${videoNumber}`).remove();
            })
            container.appendChild(deleteIcon);

            inputVideo.value = '';
            addVideoBtn.classList.add('disabled');
        }
    }

    let videoNumber = 0;
    addVideoBtn.addEventListener('click', function () {
        addVideo(videoNumber, inputVideo.value);
        videoNumber++;
    })

    inputVideo.addEventListener('keyup', function () {
        if (this.value) {
            addVideoBtn.classList.remove('disabled');
        } else {
            addVideoBtn.classList.add('disabled');
        }
    })

});
