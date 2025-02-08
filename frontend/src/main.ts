import "toastify-js/src/toastify.css";

import { __REGISTER_USER_INFO_EVENT__ } from "./constants";
import { getApp } from "./lib/getApp";
import { Menu } from "./lib/menu";
import { Router } from "./lib/router";
import { getSession } from "./lib/sessionUtils";
import { setupMenuLinks } from "./lib/setupMenuLinks";
import { setupSession } from "./lib/setupSession";
import { setupUserInfo } from "./lib/setupUserInfo";
import { template } from "./lib/template";
import { authMiddleware } from "./middlewares/authMiddleware";
import { menuMiddleware } from "./middlewares/menuMiddleware";
import authLogoutViewInHtml from "./views/AuthLogoutViewInHtml";

const menu = new Menu();
const router = new Router(getApp());

menu.setup();

setupSession().then(() => {
	router.use(menuMiddleware(menu), authMiddleware);

	authLogoutViewInHtml.registerLogoutButton();
	setLoggedUserInfos();
});

router.path("/reservations/report", async (ctx) => {
	const { ReservationReportViewInHtml } = await import(
		"./views/ReservationReportViewInHtml"
	);

	new ReservationReportViewInHtml(ctx).report();
});

router.path("/", async (ctx) => {
	const { ReservationListViewInHtml } = await import(
		"./views/ReservationListViewInHtml"
	);

	new ReservationListViewInHtml(ctx).findAll();
});

router.path("/register/reservation", async (ctx) => {
	const { CreateReservationViewInHtml } = await import(
		"./views/CreateReservationViewInHtml"
	);

	new CreateReservationViewInHtml(ctx).create();
});

router.path("/tables-being-attended", async (ctx) => {
	const { TablesBeingAttendedViewInHtml } = await import(
		"./views/TablesBeingAttendedViewInHtml"
	);

	new TablesBeingAttendedViewInHtml(ctx).draw();
});

router.path("/login", async (ctx) => {
	const { AuthLoginViewInHtml } = await import("./views/AuthLoginViewInHtml");

	new AuthLoginViewInHtml(ctx).login();
});

router.path("/404", async (ctx) => {
	ctx.render(template`
    <section class="section">
      <h3 class="title is-size-3-desktop is-size-4-mobile has-text-centered">
        Página não encontrada
      </h3>
    </section>
  `);
});

window.addEventListener(__REGISTER_USER_INFO_EVENT__, setLoggedUserInfos);

function setLoggedUserInfos() {
	const session = getSession();

	if (session?.session?.user) {
		setupUserInfo(session.session.user);

		if (session.session.user.type !== "manager") return;

		setupMenuLinks();

		router.path("/sales-by-category", async (ctx) => {
			const { ReportViewInHtml } = await import("./views/ReportViewInHtml");

			new ReportViewInHtml(
				ctx,
				"sales-by-category",
				"Relatório de vendas por categoria",
			).draw();
		});

		router.path("/sales-by-payment-method", async (ctx) => {
			const { ReportViewInHtml } = await import("./views/ReportViewInHtml");

			new ReportViewInHtml(
				ctx,
				"sales-by-payment-method",
				"Relatório de vendas por método de pagamento",
			).draw();
		});

		router.path("/sales-by-employee", async (ctx) => {
			const { ReportViewInHtml } = await import("./views/ReportViewInHtml");

			new ReportViewInHtml(
				ctx,
				"sales-by-employee",
				"Relatório de vendas por funcionário",
			).draw();
		});

		router.path("/sales-by-date", async (ctx) => {
			const { ReportViewInHtml } = await import("./views/ReportViewInHtml");

			new ReportViewInHtml(
				ctx,
				"sales-by-day",
				"Relatório de vendas por dia",
			).draw();
		});
	}
}

router.init();
