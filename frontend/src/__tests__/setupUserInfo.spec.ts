import { fireEvent, getByTestId } from "@testing-library/dom";
import { beforeEach, describe, expect, it } from "vitest";
import { __REGISTER_USER_INFO_EVENT__ } from "../constants";
import { setSession } from "../lib/sessionUtils";
import { setupUserInfo } from "../lib/setupUserInfo";
import type { AuthDTO, User } from "../models/Auth/Auth";

const mockSession: AuthDTO = {
	session: {
		user: {
			id: 1,
			login: "test",
			name: "test",
			type: "attendant",
		},
	},
};

describe("setupUserInfo", () => {
	beforeEach(() => {
		sessionStorage.clear();

		document.body.innerHTML = "";
	});

	it("should setup the user info correctly", () => {
		document.body.innerHTML = `<p class="menu__header--user-info" data-testid="user-info"></p>`;

		setSession(mockSession);

		setupUserInfo(mockSession.session?.user as User);

		expect(getByTestId(document.body, "user-info")).toHaveTextContent(
			"Olá, test",
		);
	});
});
