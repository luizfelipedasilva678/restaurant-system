import { fireEvent, getByTestId } from "@testing-library/dom";
import Toastify from "toastify-js";
import { beforeEach, describe, expect, it, vi } from "vitest";
import { createReservationForm } from "../components/createReservationForm";
import { push } from "../lib/push";
import { CreateReservationViewInHtml } from "../views/CreateReservationViewInHtml";

const tablesMock = [{ id: 1, number: 1 }];

const employeesMock = [{ id: 1, name: "Employee one" }];

const mocks = vi.hoisted(() => {
	const createReservationMock = vi.fn();
	const getTables = vi.fn(() => tablesMock);
	const getEmployees = vi.fn(() => ({ data: employeesMock }));

	return {
		presenterMock: vi.fn(() => ({
			createReservation: createReservationMock,
			getTables,
			getEmployees,
			formatDateToDateDTO: vi.fn(() => "2024-12-06 14:30:00"),
		})),
		toastifyMock: vi.fn(),
	};
});

vi.mock("../lib/sessionUtils", () => ({
	getSession: vi.fn(() => ({
		session: {
			user: {
				id: 1,
				name: "test",
				email: "test@email.com",
				userType: "attendant",
			},
		},
	})),
}));

vi.mock("../presenters/CreateReservationPresenter", () => ({
	CreateReservationPresenter: mocks.presenterMock,
}));

vi.mock("toastify-js", { spy: true });

describe("CreateReservationViewInHtml", () => {
	let view: CreateReservationViewInHtml;

	beforeEach(() => {
		vi.clearAllMocks();

		document.body.innerHTML = "";

		view = new CreateReservationViewInHtml({
			currentPage: "test",
			params: {},
			push: push,
			searchParams: new URLSearchParams(),
			render: (fragment: DocumentFragment) => {
				document.body.appendChild(fragment);
			},
		});
	});

	it("should run the flow to create a reservation correctly", async () => {
		const renderMock = vi.fn();
		view.context.render = renderMock;

		await view.create();

		const resultView = createReservationForm.render({
			employees: employeesMock,
			tables: tablesMock,
			loggedUserId: 1,
		});

		expect(view.template.innerHTML).toBe(resultView);
		expect(renderMock).toHaveBeenCalledWith(view.template.content);
	});

	it("should call the presenter method correctly on creation", async () => {
		vi.useFakeTimers();
		vi.setSystemTime(new Date(2024, 11, 6));

		await view.create();

		fireEvent.change(getByTestId(document.body, "field-phone"), {
			target: { value: "(22) 2222-2222" },
		});

		fireEvent.change(getByTestId(document.body, "field-name"), {
			target: { value: "Pedro" },
		});

		fireEvent.change(getByTestId(document.body, "field-reservation"), {
			target: { value: "2024-12-06T14:30" },
		});

		fireEvent.change(getByTestId(document.body, "table-select"), {
			target: { value: "1" },
		});

		fireEvent.change(getByTestId(document.body, "employee-select"), {
			target: { value: "1" },
		});

		fireEvent.submit(getByTestId(document.body, "create-reservation-form"));

		expect(mocks.presenterMock().createReservation).toHaveBeenCalledWith({
			clientName: "Pedro",
			clientPhone: "(22) 2222-2222",
			startTime: "2024-12-06 14:30:00",
			tableId: 1,
			employeeId: 1,
		});
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

	it("should show a toast when onError is called", () => {
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
