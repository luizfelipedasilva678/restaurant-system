import { fireEvent, getByTestId } from "@testing-library/dom";
import { beforeEach, describe, expect, it } from "vitest";
import { reportForm } from "../components/reportForm";

describe("reportForm", () => {
	beforeEach(() => {
		document.body.innerHTML = reportForm.render();
	});

	it("should render the component correctly", () => {
		expect(getByTestId(document.body, "get-report-form")).toBeInTheDocument();
		expect(getByTestId(document.body, "initial-date")).toBeInTheDocument();
		expect(getByTestId(document.body, "final-date")).toBeInTheDocument();
		expect(getByTestId(document.body, "get-report-button")).toBeInTheDocument();
	});

	it("should update field values correctly", () => {
		const initialDate = getByTestId(document.body, "initial-date");
		const finalDate = getByTestId(document.body, "final-date");

		fireEvent.change(initialDate, {
			target: { value: "2024-12-06" },
		});

		expect(initialDate).toHaveValue("2024-12-06");

		fireEvent.change(finalDate, {
			target: { value: "2024-12-06" },
		});

		expect(finalDate).toHaveValue("2024-12-06");
	});
});
