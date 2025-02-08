import { fireEvent, getByTestId } from "@testing-library/dom";
import { beforeEach, describe, expect, it } from "vitest";
import { loginForm } from "../components/loginForm";

describe("loginForm", () => {
	beforeEach(() => {
		document.body.innerHTML = loginForm.render();
	});

	it("should render the component correctly", () => {
		expect(getByTestId(document.body, "login-form")).toBeInTheDocument();
		expect(getByTestId(document.body, "login-field")).toBeInTheDocument();
		expect(getByTestId(document.body, "password-field")).toBeInTheDocument();
		expect(getByTestId(document.body, "login-button")).toBeInTheDocument();
		expect(
			getByTestId(document.body, "cancel-login-button"),
		).toBeInTheDocument();
	});

	it("should update field values correctly", () => {
		const login = getByTestId(document.body, "login-field");
		const password = getByTestId(document.body, "password-field");

		fireEvent.change(login, {
			target: { value: "test" },
		});

		expect(login).toHaveValue("test");

		fireEvent.change(password, {
			target: { value: "test" },
		});

		expect(password).toHaveValue("test");
	});
});
