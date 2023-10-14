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

	const inputFile = document.getElementById('avatar-input-file');
	if (inputFile) {
		const avatar = document.querySelector('.avatar');
		const inputText = document.querySelector('.avatar-input-text input');

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
			}
		})
	}
})
