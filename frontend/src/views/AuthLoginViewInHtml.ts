import { loginForm } from "../components/loginForm";
import {
	__REGISTER_LOGOUT_BUTTON_EVENT__,
	__REGISTER_USER_INFO_EVENT__,
} from "../constants";
import type { Context } from "../lib/router";
import { getSession } from "../lib/sessionUtils";
import { AuthPresenter } from "../presenters/AuthPresenter";
import { BaseView } from "./BaseView";

export class AuthLoginViewInHtml extends BaseView implements AuthView {
	private readonly presenter: AuthPresenter;

	constructor(context: Context) {
		super(context);

		this.presenter = new AuthPresenter(this);
	}

	login() {
		if (getSession()?.session?.user) {
			this.context.push("/");

			return;
		}

		this.template.innerHTML = loginForm.render();

		const loginInput = this.template.content.getElementById(
			"login",
		) as HTMLInputElement;

		const passwordInput = this.template.content.getElementById(
			"password",
		) as HTMLInputElement;

		const form = this.template.content.getElementById(
			"login-form",
		) as HTMLFormElement;

		form.addEventListener("submit", (event) => {
			event.preventDefault();

			this.presenter.login(loginInput.value, passwordInput.value);
		});

		this.context.render(this.template.content);
	}

	public onSuccess(message: string, duration?: number): void {
		super.onSuccess(message, duration);

		dispatchEvent(new CustomEvent(__REGISTER_LOGOUT_BUTTON_EVENT__));
		dispatchEvent(new CustomEvent(__REGISTER_USER_INFO_EVENT__));
	}
}
