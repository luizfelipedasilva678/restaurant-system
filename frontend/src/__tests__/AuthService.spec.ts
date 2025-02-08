import { beforeEach, describe, expect, it, vi } from "vitest";
import type { AuthDTO } from "../models/Auth/Auth";
import { AuthService } from "../models/Auth/AuthService";

const mockSession: AuthDTO = {
	session: {
		user: { login: "test", id: 1, name: "test", userType: "attendant" },
	},
};

describe("AuthService", () => {
	let service = new AuthService();

	beforeEach(() => {
		service = new AuthService();
	});

	it("should find the session correctly", async () => {
		vi.spyOn(service, "findSession").mockReturnValueOnce(
			Promise.resolve(mockSession),
		);

		const session = await service.findSession();

		expect(session).toEqual(mockSession);
	});

	it("should login correctly", async () => {
		const response = { message: "sessão iniciada com sucesso" };

		vi.spyOn(service, "login").mockReturnValueOnce(Promise.resolve(response));

		const session = await service.login("test", "123");

		expect(session).toEqual(response);
	});

	it("should logout correctly", async () => {
		const response = { message: "sessão destruída com sucesso" };

		vi.spyOn(service, "logout").mockReturnValueOnce(Promise.resolve(response));

		const session = await service.logout();

		expect(session).toEqual(response);
	});
});
