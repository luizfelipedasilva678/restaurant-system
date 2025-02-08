export function getApp() {
	let app = document.getElementById("app");

	if (app) {
		return app;
	}

	app = document.createElement("div");
	app.id = "app";

	return app;
}
