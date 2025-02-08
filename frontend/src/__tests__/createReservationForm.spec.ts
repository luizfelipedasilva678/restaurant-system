import { fireEvent, getByTestId } from "@testing-library/dom";
import { beforeEach, describe, expect, it } from "vitest";
import { createReservationForm } from "../components/createReservationForm";

describe("createReservationForm", () => {
	beforeEach(() => {
		document.body.innerHTML = createReservationForm.render({
			employees: [
				{ id: 1, name: "employee 1" },
				{ id: 2, name: "employee 2" },
			],
			tables: [
				{ id: 1, number: 1 },
				{ id: 2, number: 2 },
			],
			loggedUserId: 1,
		});
	});

	it("should render the component correctly", () => {
		expect(
			getByTestId(document.body, "create-reservation-form"),
		).toBeInTheDocument();
		expect(getByTestId(document.body, "field-name")).toBeInTheDocument();
		expect(getByTestId(document.body, "field-phone")).toBeInTheDocument();
		expect(getByTestId(document.body, "field-reservation")).toBeInTheDocument();
		expect(getByTestId(document.body, "table-select")).toBeInTheDocument();
		expect(getByTestId(document.body, "employee-select")).toBeInTheDocument();
		expect(
			getByTestId(document.body, "create-reservation"),
		).toBeInTheDocument();
		expect(
			getByTestId(document.body, "cancel-reservation"),
		).toBeInTheDocument();
	});

	it("should render the correct number of options for table select", () => {
		const tableSelect = getByTestId(document.body, "table-select");

		expect(tableSelect.children).toHaveLength(3);
	});

	it("should render the correct number of options for employee select", () => {
		const employeeSelect = getByTestId(document.body, "employee-select");

		expect(employeeSelect.children).toHaveLength(3);
	});

	it("should update field values correctly", () => {
		const fieldName = getByTestId(document.body, "field-name");
		const fieldPhone = getByTestId(document.body, "field-phone");
		const fieldReservation = getByTestId(document.body, "field-reservation");
		const tableSelect = getByTestId(document.body, "table-select");
		const employeeSelect = getByTestId(document.body, "employee-select");

		fireEvent.change(fieldName, {
			target: { value: "Teste" },
		});

		expect(fieldName).toHaveValue("Teste");

		fireEvent.change(fieldPhone, {
			target: { value: "(22) 2222-2222" },
		});

		expect(fieldPhone).toHaveValue("(22) 2222-2222");

		fireEvent.change(fieldReservation, {
			target: { value: "2024-12-06T14:30" },
		});

		expect(fieldReservation).toHaveValue("2024-12-06T14:30");

		fireEvent.change(tableSelect, { target: { value: "1" } });

		expect(tableSelect).toHaveValue("1");

		fireEvent.change(employeeSelect, { target: { value: "1" } });

		expect(employeeSelect).toHaveValue("1");
	});
});
