document.addEventListener("DOMContentLoaded", _ => {
	const bookBtn = document.getElementById("send");

	const nameInput = document.getElementById("name");
	const amountInput = document.getElementById("amount");
	const whenInput = document.getElementById("when");
	const phoneInput = document.getElementById("phone");
	const emailInput = document.getElementById("email");
	const notesInput = document.getElementById("notes");

	const validateData = _ => {
		[nameInput, amountInput, whenInput, phoneInput, emailInput].forEach(elm => elm.classList.remove("error"));
		ok = true;
		if (nameInput.value === "") {
			nameInput.classList.add("error");
			ok = false;
		}

		if (amountInput.value < 0 || amountInput.value > 20 || amountInput.value === "") {
			amountInput.classList.add("error");
			ok = false;
		}
		
		let insertedDate = new Date(whenInput.value);
		if (insertedDate <= Date.now() || whenInput.value === "") {
			whenInput.classList.add("error");
			ok = false;
		}

		let validatePhoneRegex = /^(\+|00)[1-9][0-9 \-\(\)\.]{7,32}$/;
		if (!validatePhoneRegex.exec(phoneInput.value)) {
			phoneInput.classList.add("error");
			ok = false;
		}

		let validateEmailRegex = /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
		if (!validateEmailRegex.exec(emailInput.value) && emailInput.value !== "") {
			emailInput.classList.add("error");
			ok = false;
		}

		return ok;
	}

	bookBtn.addEventListener("click", e => {
		e.preventDefault();

		if (!validateData()) {
			return;
		}
		
		fetch("/api/publicPerson", {
			method: "POST",
			headers: new Headers({"Content-Type": "application/json"}),
			body: JSON.stringify({
				name: nameInput.value,
				email: emailInput.value,
				phone: phoneInput.value
			})
		}).then(body => body.json())
		.then(json => {
			console.log(json);
			fetch("/api/publicOrder", {
				method: "POST",
				headers: new Headers({"Content-Type": "application/json"}),
				body: JSON.stringify({
					amount: parseInt(amountInput.value),
					notes: notesInput.value,
					at: new Date(whenInput.value).toISOString(),
					booker: json.id
				})
			}).then(body => body.json())
			.then(json => {
				console.log(json)
			})
		}).catch(err => console.error(err));
	});
});
