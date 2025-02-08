import Toastify from "toastify-js";
import { beforeEach, describe, expect, it, vi } from "vitest";
import { loginForm } from "../components/loginForm";
import { push } from "../lib/push";
import { setSession } from "../lib/sessionUtils";
import { AuthLoginViewInHtml } from "../views/AuthLoginViewInHtml";

const mocks = vi.hoisted(() => {
	const mockLogin = vi.fn();
	const mockPresenter = vi.fn(() => ({ login: mockLogin }));

	return {
		mockPresenter,
		mockLogin,
	};
});

vi.mock("toastify-js", { spy: true });

vi.mock("../presenters/AuthPresenter", () => ({
	AuthPresenter: mocks.mockPresenter,
}));

describe("AuthLoginViewInHtml", () => {
	let view: AuthLoginViewInHtml;

	beforeEach(() => {
		vi.clearAllMocks();

		document.body.innerHTML = "";
		sessionStorage.clear();

		view = new AuthLoginViewInHtml({
			currentPage: "test",
			params: {},
			push,
			render: (fragment: DocumentFragment) => {
				document.body.appendChild(fragment);
			},
			searchParams: new URLSearchParams(),
		});
	});

	it("should set the template correctly", () => {
		const renderMock = vi.fn();
		view.context.render = renderMock;

		view.login();

		const resultView = loginForm.render();

		expect(view.template.innerHTML).toBe(resultView);
		expect(renderMock).toHaveBeenCalledWith(view.template.content);
	});

	it("should not add the template when the session exists", () => {
		setSession({
			session: {
				user: { id: 1, login: "test", name: "test", userType: "attendant" },
			},
		});

		const mockPush = vi.fn();

		view.context.push = mockPush;

		view.login();

		expect(mockPush).toHaveBeenCalled();
	});

	it("should show a toast when onSuccess is called", () => {
		view.onSuccess("Success message");

		expect(Toastify).toHaveBeenCalledWith({
			className: "has-background-success",
			text: "Success message",
			position: "right",
			style: {
				background:
					"hsl(var(--bulma-success-h), var(--bulma-success-s), var(--bulma-success-l))",
			},
			duration: 3000,
		});
	});

	it("should show a toast when onCreationError is called", () => {
		view.onError("Error message");

		expect(Toastify).toHaveBeenCalledWith({
			text: "Error message",
			position: "right",
			duration: 3000,
			style: {
				background:
					"hsl(var(--bulma-danger-h), var(--bulma-danger-s), var(--bulma-danger-l))",
			},
		});
	});
});
