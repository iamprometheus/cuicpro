jQuery(document).ready(function () {
	const tg = new tourguide.TourGuideClient({ rememberStep: true });
	tg.addSteps([
		{
			title: "Introduccion a la creacion de torneos",
			content: "Ahora veras paso a paso como crear un torneo.",
		},
		{
			title: "Nombre del torneo",
			content: "Introduce el nombre del torneo.",
			target: "#tournament-name",
		},
		{
			title: "Dias de juego",
			content: "Introduce los dias en los que se llevara a cabo el torneo.",
			target: "#tournament-days",
		},
		{
			title: "Horarios",
			content:
				"Introduce los horarios. Dependiendo de los dias que hayas elegido en el paso anterior, se mostrara un selector de horarios para cada dia seleccionado.",
			target: "#hours-container",
		},
		{
			title: "Cantidad de campos 5v5",
			content:
				"Introduce el numero de campos 5v5. ¿Que cantidad de campos 5v5 deseas para el torneo?",
			target: "#fields-5v5",
		},
		{
			title: "Cantidad de campos 7v7",
			content:
				"Introduce el numero de campos 7v7. ¿Que cantidad de campos 7v7 deseas para el torneo?",
			target: "#fields-7v7",
		},
		{
			title: "Acciones",
			content: "Para finalizar, presiona el boton crear y se creara el torneo.",
			target: "#add-tournament-button",
		},
		{
			title: "Resultado",
			content:
				"Si todo esta correcto, se creara el torneo y se mostrara un mensaje de exito. Si hay un error, se mostrara un mensaje de error que explicara el problema. Si crees que hay un problema con la creacion de torneos, contacte al soporte.",
			target: "#tournament-result-table",
		},
	]);
	jQuery("#create-tournament-help-button").click(function () {
		tg.start();
	});

	jQuery("#update-tournament-help-button").click(function () {
		// tg.addSteps([
		// 	{
		// 		title: "Introduccion a la modificacion de torneos",
		// 		content:
		// 			"Ahora veras paso a paso como modificar los datos de un torneo.",
		// 	},
		// 	{
		// 		title: "Selecciona el torneo que deseas modificar",
		// 		content: "Identifica que torneo quieres modificar.",
		// 		target: ".tournaments-container",
		// 	},
		// 	{
		// 		title: "Presiona el boton editar torneo",
		// 		content:
		// 			"Presiona el boton editar torneo para comenzar la modificacion de los datos del torneo.",
		// 		target: "#edit-tournament-button",
		// 	},
		// 	{
		// 		title: "Nombre del torneo",
		// 		content:
		// 			"Introduce el nuevo nombre del torneo o deja el nombre actual.",
		// 		target: "#tournament-name",
		// 	},
		// 	{
		// 		title: "Dias de juego",
		// 		content:
		// 			"Introduce los nuevos dias en los que se llevara a cabo el torneo o deja los dias actuales.",
		// 		target: "#tournament-days",
		// 	},
		// 	{
		// 		title: "Horarios",
		// 		content: "Introduce los nuevos horarios o deja los horarios actuales.",
		// 		target: "#hours-container",
		// 	},
		// 	{
		// 		title: "Cantidad de campos 5v5",
		// 		content:
		// 			"Introduce el nuevo numero de campos 5v5 o deja el numero actual.",
		// 		target: "#fields-5v5",
		// 	},
		// 	{
		// 		title: "Cantidad de campos 7v7",
		// 		content:
		// 			"Introduce el nuevo numero de campos 7v7 o deja el numero actual.",
		// 		target: "#fields-7v7",
		// 	},
		// 	{
		// 		title: "Cancelar modificacion",
		// 		content:
		// 			"Si no deseas modificar el torneo, presiona el boton cancelar y se cancelara el proceso.",
		// 		target: "#cancel-tournament-button",
		// 	},
		// 	{
		// 		title: "Finalizar modificaciones",
		// 		content:
		// 			"Para finalizar, presiona el boton actualizar y se actualizara el torneo.",
		// 		target: "#update-tournament-button",
		// 	},
		// 	{
		// 		title: "Resultado",
		// 		content:
		// 			"Si todo esta correcto, se actualizara el torneo y se mostrara un mensaje de exito. Si hay un error, se mostrara un mensaje de error que explicara el problema. Si crees que hay un problema con la modificacion de torneos, contacte al soporte.",
		// 		target: "#tournament-result-table",
		// 	},
		// ]);
		// tg.start();
	});

	jQuery("#delete-tournament-help-button").click(async function () {});
});
