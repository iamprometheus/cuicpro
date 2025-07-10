jQuery(document).ready(function () {
	const tg = new tourguide.TourGuideClient({ rememberStep: true });

	tg.addSteps([
		{
			title: "Introduccion a la creacion de torneos",
			content: "Ahora veras paso a paso como crear un torneo.",
			group: "create-tournament",
		},
		{
			title: "Nombre del torneo",
			content: "Introduce el nombre del torneo.",
			target: "#tournament-name",
			group: "create-tournament",
		},
		{
			title: "Dias de juego",
			content: "Introduce los dias en los que se llevara a cabo el torneo.",
			target: "#tournament-days",
			group: "create-tournament",
		},
		{
			title: "Horarios",
			content:
				"Introduce los horarios. Dependiendo de los dias que hayas elegido en el paso anterior, se mostrara un selector de horarios para cada dia seleccionado.",
			target: "#hours-container",
			group: "create-tournament",
		},
		{
			title: "Cantidad de campos 5v5",
			content:
				"Introduce el numero de campos 5v5. ¿Que cantidad de campos 5v5 deseas para el torneo?",
			target: "#fields-5v5",
			group: "create-tournament",
		},
		{
			title: "Cantidad de campos 7v7",
			content:
				"Introduce el numero de campos 7v7. ¿Que cantidad de campos 7v7 deseas para el torneo?",
			target: "#fields-7v7",
			group: "create-tournament",
		},
		{
			title: "Acciones",
			content: "Para finalizar, presiona el boton crear y se creara el torneo.",
			target: "#add-tournament-button",
			group: "create-tournament",
		},
		{
			title: "Resultado",
			content:
				"Si todo esta correcto, se creara el torneo y se mostrara un mensaje de exito. Si hay un error, se mostrara un mensaje de error que explicara el problema. Si crees que hay un problema con la creacion de torneos, contacte al soporte.",
			target: "#tournament-result-table",
			group: "create-tournament",
		},
	]);

	tg.addSteps([
		{
			title: "Introduccion a la modificacion de torneos",
			content: "Ahora veras paso a paso como modificar los datos de un torneo.",
			group: "update-tournament",
		},
		{
			title: "Selecciona el torneo que deseas modificar",
			content: "Identifica que torneo quieres modificar.",
			target: "#tournaments-container",
			group: "update-tournament",
		},
		{
			title: "Presiona el boton editar torneo",
			content:
				"Presiona el boton editar torneo para comenzar la modificacion de los datos del torneo.",
			target: "#edit-tournament-button",
			group: "update-tournament",
		},
		{
			title: "Nombre del torneo",
			content: "Introduce el nuevo nombre del torneo o deja el nombre actual.",
			target: "#tournament-name",
			group: "update-tournament",
		},
		{
			title: "Dias de juego",
			content:
				"Introduce los nuevos dias en los que se llevara a cabo el torneo o deja los dias actuales.",
			target: "#tournament-days",
			group: "update-tournament",
		},
		{
			title: "Horarios",
			content: "Introduce los nuevos horarios o deja los horarios actuales.",
			target: "#hours-container",
			group: "update-tournament",
		},
		{
			title: "Cantidad de campos 5v5",
			content:
				"Introduce el nuevo numero de campos 5v5 o deja el numero actual.",
			target: "#fields-5v5",
			group: "update-tournament",
		},
		{
			title: "Cantidad de campos 7v7",
			content:
				"Introduce el nuevo numero de campos 7v7 o deja el numero actual.",
			target: "#fields-7v7",
			group: "update-tournament",
		},
		{
			title: "Cancelar modificacion",
			content:
				"Si no deseas modificar el torneo, presiona el boton cancelar y se cancelara el proceso.",
			target: "#cancel-tournament-button",
			group: "update-tournament",
		},
		{
			title: "Finalizar modificaciones",
			content:
				"Para finalizar, presiona el boton actualizar y se actualizara el torneo.",
			target: "#update-tournament-button",
			group: "update-tournament",
		},
		{
			title: "Resultado",
			content:
				"Si todo esta correcto, se actualizara el torneo y se mostrara un mensaje de exito. Si hay un error, se mostrara un mensaje de error que explicara el problema. Si crees que hay un problema con la modificacion de torneos, contacte al soporte.",
			target: "#tournament-result-table",
			group: "update-tournament",
		},
	]);

	tg.addSteps([
		{
			title: "Introduccion a la eliminacion de torneos",
			content: "Ahora veras paso a paso como eliminar un torneo.",
			group: "delete-tournament",
		},
		{
			title: "Selecciona el torneo que deseas eliminar",
			content: "Identifica que torneo quieres eliminar.",
			target: "#tournaments-container",
			group: "delete-tournament",
		},
		{
			title: "Presiona el boton eliminar torneo",
			content:
				"Presiona el boton eliminar torneo para comenzar la eliminacion del torneo. No te preocupes, al dar click en el boton no se eliminara de inmediato, se mostrara antes otra recuadro para confirmar la eliminacion.",
			target: "#delete-tournament-button",
			group: "delete-tournament",
		},
		{
			title: "Resultado",
			content:
				"Si todo esta correcto, se eliminara el torneo y se mostrara un mensaje de exito. Si hay un error, se mostrara un mensaje de error que explicara el problema. Si crees que hay un problema con la eliminacion de torneos, contacte al soporte.",
			target: "#tournament-result-table",
			group: "delete-tournament",
		},
	]);

	tg.addSteps([
		{
			title: "Introduccion a la gestion de partidos",
			content:
				"Ahora veras paso a paso como crear y eliminar los partidos de un torneo.",
			group: "tournament-matches",
		},
		{
			title: "Selecciona el torneo que deseas gestionar",
			content: "Identifica a que torneo le deseas crear o eliminar partidos.",
			target: "#tournaments-container",
			group: "tournament-matches",
		},
		{
			title: "Tipos de torneo: Eliminacion directa",
			content:
				"Selecciona el tipo de torneo que deseas crear. En el torneo de eliminacion directa, se crean brackets de eliminacion directa donde cada equipo se enfrentara directamente a otro equipo e ira avanzando el ganador de cada partido hasta el final del torneo. Todo se realiza de forma automatica una vez que presionas el boton.",
			target: "#create-brackets-button",
			group: "tournament-matches",
		},
		{
			title: "Tipos de torneo: Liguilla",
			content:
				"Selecciona el tipo de torneo que deseas crear. En el torneo de liguilla, se crean brackets de liguilla donde cada equipo se enfrentara a los equipo de su misma division, garantizando que cada equipo tenga un partido con cada equipo de su division. Todo se realiza de forma automatica una vez que presionas el boton.",
			target: "#create-brackets-button",
			group: "tournament-matches",
		},
		{
			title: "Eliminacion de partidos",
			content:
				"Si deseas eliminar los partidos ya creados, presiona el boton eliminar partidos. Esta accion elimina todos los partidos del torneo, util cuando deseas cambiar algun detalle o agregar nuevas divisiones o equipos al torneo. No te preocupes, al dar click en el boton no se eliminara de inmediato, se mostrara antes otra recuadro para confirmar la eliminacion.",
			target: "#delete-matches-button",
			group: "tournament-matches",
		},
		{
			title: "Resultado",
			content:
				"Si todo esta correcto, realizara la accion que hayas seleccionado (eliminacion directa, liguilla, eliminar partidos) y se mostrara un mensaje de exito. Si hay un error, se mostrara un mensaje de error que explicara el problema. Si crees que hay un problema con esta seccion, contacte al soporte.",
			target: "#tournament-result-table-container",
			group: "tournament-matches",
		},
	]);

	tg.addSteps([
		{
			title: "Introduccion a la gestion de arbitros",
			content:
				"Ahora veras paso a paso como gestionar los arbitros de un torneo.",
			group: "tournament-officials",
		},
		{
			title: "Selecciona el torneo que deseas gestionar",
			content:
				"Identifica a que torneo le deseas asignar o desasignar arbitros.",
			target: "#tournaments-container",
			group: "tournament-officials",
		},
		{
			title: "Agregar arbitros",
			content:
				"Si deseas asignar los arbitros, presiona el boton 'asignar arbitros'. Esta accion asignara los arbitros a los partidos del torneo, priorizando aquellos arbitros con certificacion.",
			target: "#delete-matches-button",
			group: "tournament-officials",
		},
		{
			title: "Desasignar arbitros",
			content:
				"Si deseas desasignar los arbitros, presiona el boton 'desasignar arbitros'. Esta accion desasignara los arbitros de los partidos del torneo, util cuando quieres cambiar los datos de algun arbitro, agregar nuevos arbitros o eliminar algunos.",
			target: "#delete-matches-button",
			group: "tournament-officials",
		},
		{
			title: "Resultado",
			content:
				"Si todo esta correcto, realizara la accion que hayas seleccionado (asignar arbitros, desasignar arbitros) y se mostrara un mensaje de exito. Si hay un error, se mostrara un mensaje de error que explicara el problema. Si crees que hay un problema con esta seccion, contacte al soporte.",
			target: "#tournament-result-table-container",
			group: "tournament-officials",
		},
	]);

	tg.addSteps([
		{
			title: "Introduccion a la finalizacion de torneos",
			content: "Ahora veras paso a paso como finalizar un torneo.",
			group: "finish-tournament",
		},
		{
			title: "Selecciona el torneo que deseas gestionar",
			content: "Identifica a que torneo le deseas finalizar.",
			target: "#tournaments-container",
			group: "finish-tournament",
		},
		{
			title: "Finalizar torneo",
			content:
				"Si deseas finalizar el torneo, presiona el boton 'finalizar torneo'. Esta accion finalizara el torneo, archivandolo y guardandolo en la base de datos, toda la informacion del torneo ya no aparecera en el panel de administrador pero queda guardada en la base de datos para futuras referencias.",
			target: "#delete-matches-button",
			group: "finish-tournament",
		},
		{
			title: "Resultado",
			content:
				"Si todo esta correcto, se finalizara el torneo y se mostrara un mensaje de exito. Si hay un error, se mostrara un mensaje de error que explicara el problema. Si crees que hay un problema con esta seccion, contacte al soporte.",
			target: "#tournament-result-table-container",
			group: "finish-tournament",
		},
	]);

	jQuery("#create-tournament-help-button").click(function () {
		tg.start("create-tournament");
	});

	jQuery("#update-tournament-help-button").click(function () {
		tg.start("update-tournament");
	});

	jQuery("#delete-tournament-help-button").click(async function () {
		tg.start("delete-tournament");
	});

	jQuery("#tournament-matches-help-button").click(async function () {
		tg.start("tournament-matches");
	});

	jQuery("#tournament-officials-help-button").click(async function () {
		tg.start("tournament-officials");
	});

	jQuery("#finish-tournament-help-button").click(async function () {
		tg.start("finish-tournament");
	});
});
