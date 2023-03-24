document.addEventListener("DOMContentLoaded", _ => {
	const submit = document.getElementById("login");
	const username = document.getElementById("username");
	const password = document.getElementById("password");
	
	fetch("/api/session", {
		method: "GET",
		credentials: "same-origin"
	})
	.then(res => {
		if (res.ok) {
			document.location.href = "/dashboard"	
		}
	})
	.catch(err => {})

	submit.addEventListener("click", e => {
		e.preventDefault();

		let credentials = btoa(`${username.value}:${password.value}`);
		let headers = { "Authorization": `Basic ${credentials}` }
		fetch("/api/session", {
			method: "POST",
			headers: new Headers(headers),
			credentials: "same-origin"
		}).then(res => {
			if (!res.ok) {
				throw new Error("Incorrect credentials")
			}

			return res.json()
		})
		.then(json => {
			document.location.href="/dashboard";
		})
		.catch(err => {
			alert(err)
		})
	});
});
