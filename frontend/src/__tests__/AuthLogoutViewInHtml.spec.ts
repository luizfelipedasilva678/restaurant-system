import { beforeEach, describe, expect, it, vi } from "vitest";
import authLogoutViewInHtml from "../views/AuthLogoutViewInHtml";

const mocks = vi.hoisted(() => {
	const mockLogout = vi.fn();
	const mockPresenter = vi.fn(() => ({ logout: mockLogout }));
	const mockGetSession = vi.fn();

	return {
		mockPresenter,
		mockLogout,
		mockGetSession,
	};
});

vi.mock("toastify-js", { spy: true });

vi.mock("../presenters/AuthPresenter", () => ({
	AuthPresenter: mocks.mockPresenter,
}));

vi.mock("../lib/sessionUtils", () => ({
	getSession: mocks.mockGetSession,
}));

describe("AuthLogoutViewInHtml", () => {
	let view: typeof authLogoutViewInHtml;

	beforeEach(() => {
		vi.clearAllMocks();

		document.body.innerHTML = "";
		sessionStorage.clear();

		view = authLogoutViewInHtml;
	});

	it("should set the template correctly", () => {
		mocks.mockGetSession.mockReturnValueOnce({ session: { user: {} } });

		const renderMock = vi.fn();
		view.context.render = renderMock;

		view.registerLogoutButton();

		expect(renderMock).toHaveBeenCalled();
	});
});
