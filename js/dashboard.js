document.addEventListener("DOMContentLoaded", _ => {
	let d = new Date();
	let runtime = {
		selectedMonth: d.getMonth(),
		selectedYear: d.getFullYear(),
		selectedDay: d.getDate()
	}

	const exitBtn = document.getElementById("exit");
	const monthCalendar = document.getElementById("month-calendar");
	const previousMonth = document.getElementById("previous-month");
	const currentMonth = document.getElementById("current-month");
	const nextMonth = document.getElementById("next-month");

	const dayView = document.getElementById("day-view");
	const dayViewDay = document.getElementById("day-view-day");
	const dayViewMonth = document.getElementById("day-view-month");
	const dayViewTemplate = document.getElementById("day-view-order");
	const dayViewOrders = document.getElementById("day-view-orders");

	const thisMonthName = _ => {
		let months = ["Gennaio","Febbraio","Marzo","Aprile","Maggio","Giugno","Luglio","Agosto","Settembre","Ottobre","Novembre","Dicembre"];

		return months[runtime.selectedMonth];
	}

	const updateDayView = _ => {
		dayViewDay.textContent = runtime.selectedDay;
		dayViewMonth.textContent = thisMonthName(runtime.selectedMonth);

		fetch("/api/order?" + new URLSearchParams({
			month: runtime.selectedMonth,
			year: runtime.selectedYear
		}), { credentials: "same-origin", method: "GET" })
		.then(body => body.json())
		.then(json => {
			exitOnAuthRequired(json);
			
			dayViewOrders.innerHTML = "";

			json.forEach(order => {
				if (new Date(order.at.slice(0, order.at.length - 1)).getDate() !== runtime.selectedDay)
					return;

				let orderCard = dayViewTemplate.innerHTML;
				orderCard = orderCard.replace("{{ amount }}", order.amount)
				let when = new Date(order.at.slice(0, order.at.length - 1))
				let time = when.toLocaleTimeString("it-IT", { hour: '2-digit', minute: '2-digit' });
				orderCard = orderCard.replace("{{ time }}", time)
				orderCard = orderCard.replace("{{ id }}", order.id)
				let orderStatus;
				if (order["status"] == "BOOKED") {
					orderStatus = "<span class=oi data-glyph=timer></span>"
				} 
				if (order["status"] == "VERIFIED") {
					orderStatus = "confermato"
				}
				if (order["status"] == "ARRIVED") {
					orderStatus = "arrivato"
				}
				orderCard = orderCard.replace("{{ status }}", orderStatus)
				dayViewOrders.innerHTML += orderCard
			});
		})

	}


	document.location.href.hash = "#day-view";
	updateDayView();

	const exitOnAuthRequired = json => {
		if (json?.description === "Cookie authentication required" ||
			json?.description === "Session expired" ||
			json?.description === "Session does not exist" ||
			json?.description === "Incorrect token") {
			exitFromSession()
		}
	}

	const exitFromSession = _ => {
		fetch("/api/session", {
			credentials: "same-origin",
			method: "DELETE",
		})
		.then(body => body.json())
		.then(json => {
			exitOnAuthRequired(json)
		})
		.catch(err => {});
		document.location.href = "/login";
	}

	const daysInThisMonth = _ => {
		return new Date(runtime.selectedYear, runtime.selectedMonth + 1, 0).getDate();
	}

	const firstWeekDay = _ => {
		return new Date(runtime.selectedYear, runtime.selectedMonth, 0).getDay();
	}

	const getSpanFromDay = d => {
		child = firstWeekDay() + d - 1;
		return monthCalendar.children[child]
	}

	const updateCalendarView = _ => {
		monthCalendar.innerHTML = "";

		for (let i = 0; i < firstWeekDay(); i++) {
			monthCalendar.innerHTML += "<span class=blank></span>";
		}

		for (let i = 1; i <= daysInThisMonth(); i++ ) {
			monthCalendar.innerHTML += "<span>" + i + "</span>";
		}

		currentMonth.textContent = thisMonthName() + " - " + runtime.selectedYear;
		
		let spanDays = Array.from(monthCalendar.children)
		spanDays = spanDays.filter(elm => !elm.classList.contains("blank"))
		spanDays.forEach(elm => elm.addEventListener("click", e => {
			runtime.selectedDay = parseInt(elm.textContent);
			updateDayView();
			document.location.hash = "#day-view";
		}))

		fetch("/api/order?" + new URLSearchParams({
			month: runtime.selectedMonth,
			year: runtime.selectedYear
		}), { credentials: "same-origin", method: "GET" })
		.then(body => body.json())
		.then(json => {
			exitOnAuthRequired(json)
			json.forEach(order => {
				let when = new Date(order.at.slice(0, order.at.length - 1));
				let span = getSpanFromDay(when.getDate());
				span.classList.add("dot");
			});

		})
		.catch(err => {})
	}

	updateCalendarView();

	previousMonth.addEventListener("click", e => {
		runtime.selectedMonth -= 1;

		if (runtime.selectedMonth < 0) {
			runtime.selectedMonth = 11;
			runtime.selectedYear -= 1;
		}

		updateCalendarView();
	})

	nextMonth.addEventListener("click", e => {
		runtime.selectedMonth += 1;

		if (runtime.selectedMonth > 11) {
			runtime.selectedMonth = 0;
			runtime.selectedYear += 1;
		}

		updateCalendarView();
	})

	exitBtn.addEventListener("click", e => {
		e.preventDefault();
		exitFromSession();
	});
});
