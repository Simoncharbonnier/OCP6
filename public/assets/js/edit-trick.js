document.addEventListener("DOMContentLoaded", () => {
	// Click
	function click(target) {
		const event = new MouseEvent("click");
		target.dispatchEvent(event);
	}

	document.querySelectorAll('.icon').forEach(function (icon) {
		icon.addEventListener('click', function () {
			const bannerImage = icon.classList.contains('banner-icon') ? true : false;
			const action = icon.getAttribute('data-action');
			let container = icon.closest('.container-image') ?? icon.closest('.container-video');
			if (bannerImage) {
				container = document.querySelector('.banner');
			}
			let input = bannerImage ? document.querySelector('.images input') : document.getElementById(container.getAttribute('data-input'));

			if (action === 'edit') {
				if (bannerImage || container.getAttribute('data') === 'image') {
					if (input === null) {
						const inputContainer = document.createElement('div');
						inputContainer.innerHTML = document.getElementById('trick_form_images').getAttribute('data-prototype').replace(/__name__/g, 0);
						inputContainer.classList.add('d-none');
						document.querySelector('.images').appendChild(inputContainer);
						input = inputContainer.querySelector('input');
					}

					const inputFile = document.createElement('input');
					inputFile.type = 'file';
					inputFile.classList.add('d-none');
					container.appendChild(inputFile);

					inputFile.addEventListener('change', function () {
						const file = inputFile.files[0];
						const reader = new FileReader();
						reader.readAsDataURL(file);

						reader.onloadend = function () {
							if (bannerImage) {
								container.style.backgroundImage = "url('" + reader.result + "')";
								document.querySelector('.banner .icons .delete').classList.remove('d-none');
							} else {
								container.querySelector('img').src = reader.result;
							}
							input.value = reader.result;
						}
					});

					click(inputFile);
				} else {
					const modal = document.getElementById('modal-edit-video');
					modal.setAttribute('data-edit', input.id);
				}
			} else if (action === 'delete') {
				const modal = document.getElementById('modal-delete');
				modal.setAttribute('data-delete', input.id);
				if (bannerImage) {
					modal.setAttribute('data-banner', true);
				}
			}
		})
	});

	const modalEditVideo = document.getElementById('modal-edit-video');

	modalEditVideo.querySelectorAll('.close-modal').forEach(function (btn) {
		btn.addEventListener('click', function () {
			setTimeout(function () {
				modalEditVideo.querySelector('input').value = '';
			}, 200)
		})
	});

	modalEditVideo.querySelector('.btn-validate').addEventListener('click', function () {
		let valueMatched = modalEditVideo.querySelector('input').value.match(/watch\?v=([^&]*)/);
		if (valueMatched) {
			valueMatched = valueMatched[1];
			const input = document.getElementById(modalEditVideo.getAttribute('data-edit'));
			const container = document.getElementById('container_' + modalEditVideo.getAttribute('data-edit'));

			container.querySelector('iframe').src = "https://www.youtube.com/embed/" + valueMatched;
			input.value = valueMatched;
		}
	})

	const modalDelete = document.getElementById('modal-delete');

	modalDelete.querySelector('.btn-delete').addEventListener('click', function () {
		const bannerImage = modalDelete.getAttribute('data-banner');
		const input = document.getElementById(modalDelete.getAttribute('data-delete'));
		const container = bannerImage ? document.querySelector('.banner') : document.getElementById('container_' + modalDelete.getAttribute('data-delete'));

		input.remove();
		if (bannerImage) {
			let imageValue = '';
			if (document.querySelector('.images input')) {
				const value = document.querySelector('.images input').value;
				imageValue = value.match(/\.jpg$/) ? '/assets/img/tricks/' + value : value;
			} else {
				imageValue = '/assets/img/snowtricks_banner.jpg';
				document.querySelector('.banner .icons .delete').classList.add('d-none');
			}

			container.style.backgroundImage = "url('" + imageValue + "')";
			document.querySelector('.container-image').remove();
		} else {
			container.remove();
		}
	})
});
