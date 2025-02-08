import { beforeEach, describe, expect, it, vi } from "vitest";
import { UnauthorizedException } from "../exceptions/unauthorized-exception";
import { getSession } from "../lib/sessionUtils";
import type { AuthDTO } from "../models/Auth/Auth";
import { AuthPresenter } from "../presenters/AuthPresenter";

const mockSession: AuthDTO = {
	session: {
		user: { login: "test", id: 1, name: "test", userType: "attendant" },
	},
};

const mocks = vi.hoisted(() => {
	const mockLogin = vi.fn();
	const mockLogout = vi.fn();
	const mockFindSession = vi.fn();

	const mockAuthService = vi.fn(() => ({
		login: mockLogin,
		logout: mockLogout,
		findSession: mockFindSession,
	}));

	const mockOnError = vi.fn();
	const mockOnSuccess = vi.fn();

	return {
		viewMock: {
			onError: mockOnError,
			onSuccess: mockOnSuccess,
		},
		mockLogin,
		mockLogout,
		mockFindSession,
		authServiceMock: mockAuthService,
	};
});

vi.mock("../models/Auth/AuthService", () => ({
	AuthService: mocks.authServiceMock,
}));

describe("AuthPresenter", () => {
	let presenter: AuthPresenter;

	beforeEach(() => {
		vi.clearAllMocks();

		presenter = new AuthPresenter(mocks.viewMock as unknown as AuthView);
	});

	it("should login correctly", async () => {
		mocks.mockFindSession.mockReturnValueOnce(mockSession);

		await presenter.login("user", "123");

		expect(mocks.mockLogin).toHaveBeenCalledWith("user", "123");
		expect(mocks.mockFindSession).toReturnWith(mockSession);
		expect(getSession()).toEqual(mockSession);
		expect(mocks.viewMock.onSuccess).toHaveBeenCalledWith(
			"Login realizado com sucesso",
		);
	});

	it("should show the message 'Usuário ou senha inválidos' when the input is incorrect", async () => {
		mocks.mockLogin.mockRejectedValueOnce(new UnauthorizedException());

		await presenter.login("user", "123");

		expect(mocks.viewMock.onError).toHaveBeenCalledWith(
			"Usuário ou senha inválidos",
		);
	});

	it("should show the message 'Erro ao autenticar usuário' when something goes wrong", async () => {
		mocks.mockLogin.mockRejectedValueOnce(new Error());

		await presenter.login("user", "123");

		expect(mocks.viewMock.onError).toHaveBeenCalledWith(
			"Erro ao autenticar usuário",
		);
	});

	it("should logout the user correctly", async () => {
		await presenter.logout();

		expect(mocks.mockLogout).toHaveBeenCalled();
		expect(getSession()).toBeNull();
		expect(mocks.viewMock.onSuccess).toHaveBeenCalledWith(
			"Sessão finalizada com sucesso",
		);
	});

	it("should show the message 'Erro ao finalizar sessão' when logout does wrong", async () => {
		mocks.mockLogout.mockRejectedValueOnce(new Error());

		await presenter.logout();

		expect(mocks.viewMock.onError).toHaveBeenCalledWith(
			"Erro ao finalizar sessão",
		);
	});

	it("should get the session correctly", async () => {
		mocks.mockFindSession.mockReturnValueOnce(mockSession);

		const session = await presenter.getCurrentSession();

		expect(session).toEqual(mockSession);
	});

	it("should return null when there is no session", async () => {
		const session = await presenter.getCurrentSession();

		expect(session).toBeNull();
	});

	it("should return null when something goes wrong", async () => {
		mocks.mockFindSession.mockRejectedValueOnce(new Error());

		const session = await presenter.getCurrentSession();

		expect(session).toBeNull();
	});
});
