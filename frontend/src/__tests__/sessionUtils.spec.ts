import { describe, expect, it } from "vitest";
import { deleteSession, getSession, setSession } from "../lib/sessionUtils";
import type { AuthDTO } from "../models/Auth/Auth";

const mockSession: AuthDTO = {
	session: {
		user: {
			id: 1,
			login: "test",
			name: "test",
			userType: "attendant",
		},
	},
};

describe("sessionUtils", () => {
	it("should return null when there is no session", () => {
		expect(getSession()).toBeNull();
	});

	it("should set the session correctly", () => {
		setSession(mockSession);

		expect(getSession()).toEqual(mockSession);
	});

	it("should delete the session correctly", () => {
		setSession(mockSession);

		deleteSession();

		expect(getSession()).toBeNull();
	});
});
