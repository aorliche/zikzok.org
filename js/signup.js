window.addEventListener('load', e => {
	const email = document.querySelector('#email');
	const username = document.querySelector('#username');
	const password = document.querySelector('#password');
	const confirm = document.querySelector('#confirm');
	const button = document.querySelector('#button');
	const infoDiv = document.querySelector('#infoDiv');

	button.addEventListener('click', e => {
		let error = [];
		if (!email.value) {
			error.push('Email cannot be empty');
		} else if (!username.value) {
			error.push('Username cannot be empty');
		} else if (!password.value) {
			error.push('Password cannot be empty');
		} else if (confirm.value != password.value) {
			error.push('Password and confirmation are different');
		}

		if (error.length > 0) {
			e.preventDefault();
			infoDiv.innerHTML = '';
			error.forEach(err => {
				infoDiv.innerHTML += err + "<br>\n";
			});
		}
	});
});
