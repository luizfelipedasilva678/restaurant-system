import { logoutButton } from "../components/logoutButton";
import { __REGISTER_LOGOUT_BUTTON_EVENT__ } from "../constants";
import { push } from "../lib/push";
import type { Context } from "../lib/router";
import { getSession } from "../lib/sessionUtils";
import { AuthPresenter } from "../presenters/AuthPresenter";
import { BaseView } from "./BaseView";

class AuthLogoutViewInHtml extends BaseView implements AuthView {
	private readonly presenter: AuthPresenter;

	constructor(context: Context) {
		super(context);

		this.presenter = new AuthPresenter(this);

		window.addEventListener(
			__REGISTER_LOGOUT_BUTTON_EVENT__,
			this.registerLogoutButton.bind(this),
		);
	}

	async registerLogoutButton() {
		if (!getSession()?.session?.user) {
			return;
		}

		this.template.innerHTML = logoutButton.render();

		const button = this.template.content.getElementById(
			"logout-button",
		) as HTMLInputElement;

		button.addEventListener("click", async () => {
			await this.presenter.logout();

			button.remove();
		});

		this.context.render(this.template.content);
	}
}

const authLogoutViewInHtml = new AuthLogoutViewInHtml({
	currentPage: "logout",
	params: {},
	push,
	render(content) {
		const parent = document.getElementById("nav-info") as HTMLDivElement;

		parent.appendChild(content);
	},
	searchParams: new URLSearchParams(window.location.search),
});

export default authLogoutViewInHtml;
