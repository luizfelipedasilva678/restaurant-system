import { beforeEach, describe, expect, it, vi } from "vitest";
import { getSession } from "../lib/sessionUtils";
import { setupSession } from "../lib/setupSession";
import type { AuthDTO } from "../models/Auth/Auth";

const mockSession: AuthDTO = {
	session: {
		user: { login: "test", id: 1, name: "test", type: "attendant" },
	},
};

const mocks = vi.hoisted(() => {
	const mockFindSession = vi.fn();

	const mockAuthService = vi.fn(() => {
		return {
			findSession: mockFindSession,
		};
	});

	return {
		mockAuthService,
		mockFindSession,
	};
});

vi.mock("../models/Auth/AuthService", () => ({
	AuthService: mocks.mockAuthService,
}));

describe("setupSession", () => {
	beforeEach(() => {
		vi.clearAllMocks();

		sessionStorage.clear();
	});

	it("should setup the session correctly", async () => {
		mocks.mockFindSession.mockReturnValueOnce(mockSession);

		await setupSession();

		expect(getSession()).toEqual(mockSession);
	});

	it("should not set the session if something goes wrong", async () => {
		mocks.mockAuthService.mockRejectedValueOnce(new Error());

		await setupSession();

		expect(getSession()).toBeNull();
	});
});
