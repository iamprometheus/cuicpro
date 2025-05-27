jQuery(function ($) {
	jQuery("#tabs").tabs();

	let days = [];

	jQuery("#tournament-days")
		.multiDatesPicker({
			minDate: 0,
			dateFormat: "d/m/y",
			onSelect: function (dateText, inst) {
				days = inst.input.val().split(",");
				jQuery("#hours-container").empty();
				for (let i = 0; i < days.length; i++) {
					jQuery("#hours-container").append(
						`<div class='hours-slider'>
							<label for='hours-range-${i}'>${days[i]}</label>
							<input type='text' id='hours-range-${i}' readonly style='border:0; color:#f6931f; font-weight:bold;'>
							<div id='slider-hours-${i}' class='tournament-slider'></div>
						</div>`,
					);
				}
			},
		})
		.blur(function () {
			for (let i = 0; i < days.length; i++) {
				jQuery(`#slider-hours-${i}`).slider({
					range: true,
					min: 7,
					max: 23,
					step: 1,
					values: [8, 20], // Initial values (8 AM to 5 PM)
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
		});

	jQuery("#tournament-selected-days").multiDatesPicker({
		minDate: 0,
		dateFormat: "d/m/y",
	});

	jQuery("#official-schedule").multiDatesPicker({
		minDate: 0,
		dateFormat: "d/m/y",
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

	jQuery("#slider-fields-5v5").slider({
		range: true,
		min: 1,
		max: 12,
		step: 1,
		values: [1, 8], // Initial values (8 AM to 5 PM)
		slide: function (event, ui) {
			const startField = (ui.values[0] < 10 ? "0" : "") + ui.values[0];
			const endField = (ui.values[1] < 10 ? "0" : "") + ui.values[1];
			jQuery("#fields-5v5-range").val(startField + " - " + endField);
		},
	});

	const initialStartField5v5 =
		(jQuery("#slider-fields-5v5").slider("values", 0) < 10 ? "0" : "") +
		jQuery("#slider-fields-5v5").slider("values", 0);
	const initialEndField5v5 =
		(jQuery("#slider-fields-5v5").slider("values", 1) < 10 ? "0" : "") +
		jQuery("#slider-fields-5v5").slider("values", 1);
	jQuery("#fields-5v5-range").val(
		initialStartField5v5 + " - " + initialEndField5v5,
	);

	jQuery("#slider-fields-7v7").slider({
		range: true,
		min: 1,
		max: 12,
		step: 1,
		values: [9, 12], // Initial values (8 AM to 5 PM)
		slide: function (event, ui) {
			const startField = (ui.values[0] < 10 ? "0" : "") + ui.values[0];
			const endField = (ui.values[1] < 10 ? "0" : "") + ui.values[1];
			jQuery("#fields-7v7-range").val(startField + " - " + endField);
		},
	});

	const initialStartField7v7 =
		(jQuery("#slider-fields-7v7").slider("values", 0) < 10 ? "0" : "") +
		jQuery("#slider-fields-7v7").slider("values", 0);
	const initialEndField7v7 =
		(jQuery("#slider-fields-7v7").slider("values", 1) < 10 ? "0" : "") +
		jQuery("#slider-fields-7v7").slider("values", 1);
	jQuery("#fields-7v7-range").val(
		initialStartField7v7 + " - " + initialEndField7v7,
	);
});
