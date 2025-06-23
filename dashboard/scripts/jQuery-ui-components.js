jQuery(function ($) {
	const currentUrl = window.location.href;

	if (currentUrl.split("?")[1] !== "page=cuicpro") return;

	jQuery("#tabs").tabs({
		activate: function (event, ui) {
			if (ui.newTab[0].textContent !== "Brackets") {
				jQuery(".leader-line").addClass("hidden");
				jQuery("#leader-line-defs").addClass("hidden");
			} else {
				jQuery(".leader-line").removeClass("hidden");
				jQuery("#leader-line-defs").removeClass("hidden");
			}
		},
	});

	let days = [];

	jQuery("#tournament-days")
		.multiDatesPicker({
			minDate: 0,
			dateFormat: "d/m/y",
			onSelect: function (dateText, inst) {
				days = inst.input.val().split(",");

				const values = {};
				for (let i = 0; i < days.length; i++) {
					const day = jQuery(`#hours-range-${i}`).siblings("label").text();
					values[day] = jQuery(`#slider-hours-${i}`).slider("option", "values");
				}
				jQuery("#hours-container").empty();
				for (let i = 0; i < days.length; i++) {
					jQuery("#hours-container").append(
						`<div class='hours-slider'>
							<div class='hours-slider-header'>
								<label for='hours-range-${i}'>${days[i]}</label>
								<input type='text' id='hours-range-${i}' readonly style='border:0; color:black; font-weight:bold; width: 100%;'>
							</div>
							<div id='slider-hours-${i}' class='tournament-slider'></div>
						</div>`,
					);

					const values_this_day = values[days[i].trim()];

					jQuery(`#slider-hours-${i}`).slider({
						range: true,
						min: 7,
						max: 23,
						step: 1,
						values: values_this_day ? values_this_day : [10, 18],
						slide: function (event, ui) {
							const startHour = (ui.values[0] < 10 ? "0" : "") + ui.values[0];
							const endHour = (ui.values[1] < 10 ? "0" : "") + ui.values[1];
							jQuery(`#hours-range-${i}`).val(
								startHour + ":00 - " + endHour + ":00",
							);
						},
					});

					const initialStartHour =
						(jQuery(`#slider-hours-${i}`).slider("values", 0) < 10 ? "0" : "") +
						jQuery(`#slider-hours-${i}`).slider("values", 0);
					const initialEndHour =
						(jQuery(`#slider-hours-${i}`).slider("values", 1) < 10 ? "0" : "") +
						jQuery(`#slider-hours-${i}`).slider("values", 1);
					jQuery(`#hours-range-${i}`).val(
						initialStartHour + ":00 - " + initialEndHour + ":00",
					);
				}
			},
		})
		.blur(function () {
			for (let i = 0; i < days.length; i++) {}
		});

	jQuery("#tournament-selected-days").multiDatesPicker({
		minDate: 0,
		dateFormat: "d/m/y",
	});

	// fix the days of the tournament in the official schedule selector date
	const availableDays = jQuery("#official-schedule")
		.val()
		.replaceAll(" ", "")
		.split(",");

	const rawDate1 = availableDays[0].split("/").reverse();
	rawDate1[0] = "2025";
	rawDate1.join("-");
	const rawDate2 = availableDays[availableDays.length - 1].split("/").reverse();
	rawDate2[0] = "2025";
	rawDate2.join("-");

	const date1 = new Date(rawDate1);
	const date2 = new Date(rawDate2);

	jQuery("#official-schedule").multiDatesPicker({
		minDate: date1,
		maxDate: date2,
		dateFormat: "d/m/y",
		onSelect: function (dateText, inst) {
			const searchID = `#official-day-${dateText}`.replaceAll("/", "-");
			const searchID2 = `#hours-selector-${dateText}`.replaceAll("/", "-");
			const daySelector = jQuery(searchID);
			const hoursSelector = jQuery(searchID2);
			if (daySelector.hasClass("hidden")) {
				daySelector.removeClass("hidden");
			} else {
				daySelector.addClass("hidden");
				hoursSelector.addClass("hidden");
			}
		},
	});

	jQuery("#slider-hours").slider({
		range: true,
		min: 7,
		max: 23,
		step: 1,
		values: [8, 20], // Initial values (8 AM to 5 PM)
		slide: function (event, ui) {
			const startHour = (ui.values[0] < 10 ? "0" : "") + ui.values[0];
			const endHour = (ui.values[1] < 10 ? "0" : "") + ui.values[1];
			jQuery("#hours-range").val(startHour + ":00 - " + endHour + ":00");
		},
	});
});
