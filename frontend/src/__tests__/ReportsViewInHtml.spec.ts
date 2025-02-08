import { beforeEach, describe, expect, it, vi } from "vitest";
import { ReportViewInHtml } from "../views/ReportViewInHtml";
import { push } from "../lib/push";
import type {
	SalesByPaymentMethodReport,
	SalesByEmployeeReport,
	SalesByCategoryReport,
	SalesByDayReport,
} from "../models/Reports/Reports";
import { fireEvent, getByTestId } from "@testing-library/dom";

const mockSalesByPaymentMethod: SalesByPaymentMethodReport[] = [
	{
		payment_method: "Cartão de crédito",
		sales: 100,
	},
];

const mockSalesByEmployeeReport: SalesByEmployeeReport[] = [
	{
		employee: "employee one",
		sales: 100,
	},
];

const mockSalesByCategoryReport: SalesByCategoryReport[] = [
	{
		category: "Entrada",
		sales: 100,
	},
];

const mockSalesByDayReport: SalesByDayReport[] = [
	{
		date: "2024-12-06",
		sales: 100,
	},
];

const mocks = vi.hoisted(() => {
	const getReportByCategory = vi.fn();
	const getReportByDay = vi.fn();
	const getReportByEmployee = vi.fn();
	const getReportByPaymentMethod = vi.fn();

	return {
		getReportByCategory,
		getReportByDay,
		getReportByEmployee,
		getReportByPaymentMethod,
		presenterMock: vi.fn(() => ({
			getReportByCategory,
			getReportByDay,
			getReportByEmployee,
			getReportByPaymentMethod,
		})),
		toastifyMock: vi.fn(),
	};
});

vi.mock("../presenters/ReportsPresenter", () => ({
	ReportsPresenter: mocks.presenterMock,
}));

vi.mock("toastify-js", { spy: true });

vi.mock("chart.js");

const ctx = {
	currentPage: "test",
	params: {},
	push: push,
	render: (fragment: DocumentFragment) => {
		document.body.appendChild(fragment);
	},
	searchParams: new URLSearchParams(),
};

describe("ReportsViewInHtml", () => {
	let view: ReportViewInHtml;

	beforeEach(() => {
		vi.clearAllMocks();
		document.body.innerHTML = "";
	});

	it("should generate a sales by category report correctly", () => {
		view = new ReportViewInHtml(ctx, "sales-by-category", "Sales by category");
		const mockReportValidity = vi.fn(() => true);
		mocks.getReportByCategory.mockImplementationOnce(() => {
			view.showSalesByCategory(mockSalesByCategoryReport);
		});
		view.draw();

		const form = getByTestId(
			document.body,
			"get-report-form",
		) as HTMLFormElement;

		fireEvent.submit(form, {
			target: {
				"initial-date": { value: "2024-12-06" },
				"final-date": { value: "2024-12-07" },
				reportValidity: mockReportValidity,
			},
		});

		expect(mocks.getReportByCategory).toHaveBeenCalled();
	});

	it("should generate a sales by employee report correctly", () => {
		view = new ReportViewInHtml(ctx, "sales-by-employee", "Sales by employee");
		const mockReportValidity = vi.fn(() => true);
		mocks.getReportByEmployee.mockImplementationOnce(() => {
			view.showSalesByEmployee(mockSalesByEmployeeReport);
		});
		view.draw();

		const form = getByTestId(
			document.body,
			"get-report-form",
		) as HTMLFormElement;

		fireEvent.submit(form, {
			target: {
				"initial-date": { value: "2024-12-06" },
				"final-date": { value: "2024-12-07" },
				reportValidity: mockReportValidity,
			},
		});

		expect(mocks.getReportByEmployee).toHaveBeenCalled();
	});

	it("should generate a sales by day report correctly", () => {
		view = new ReportViewInHtml(ctx, "sales-by-day", "Sales by day");
		const mockReportValidity = vi.fn(() => true);
		mocks.getReportByEmployee.mockImplementationOnce(() => {
			view.showSalesByDay(mockSalesByDayReport);
		});
		view.draw();

		const form = getByTestId(
			document.body,
			"get-report-form",
		) as HTMLFormElement;

		fireEvent.submit(form, {
			target: {
				"initial-date": { value: "2024-12-06" },
				"final-date": { value: "2024-12-07" },
				reportValidity: mockReportValidity,
			},
		});

		expect(mocks.getReportByDay).toHaveBeenCalled();
	});

	it("should generate a sales by payment method report correctly", () => {
		view = new ReportViewInHtml(
			ctx,
			"sales-by-payment-method",
			"Sales by payment method",
		);
		const mockReportValidity = vi.fn(() => true);
		mocks.getReportByEmployee.mockImplementationOnce(() => {
			view.showSalesByPaymentMethod(mockSalesByPaymentMethod);
		});
		view.draw();

		const form = getByTestId(
			document.body,
			"get-report-form",
		) as HTMLFormElement;

		fireEvent.submit(form, {
			target: {
				"initial-date": { value: "2024-12-06" },
				"final-date": { value: "2024-12-07" },
				reportValidity: mockReportValidity,
			},
		});

		expect(mocks.getReportByPaymentMethod).toHaveBeenCalled();
	});

	it("should not generate a sales by category report when the form is invalid", () => {
		view = new ReportViewInHtml(ctx, "sales-by-category", "Sales by category");
		const mockReportValidity = vi.fn(() => false);
		mocks.getReportByCategory.mockImplementationOnce(() => {
			view.showSalesByCategory(mockSalesByCategoryReport);
		});
		view.draw();

		const form = getByTestId(
			document.body,
			"get-report-form",
		) as HTMLFormElement;

		fireEvent.submit(form, {
			target: {
				"initial-date": { value: "2024-12-06" },
				"final-date": { value: "2024-12-07" },
				reportValidity: mockReportValidity,
			},
		});

		expect(mocks.getReportByCategory).not.toHaveBeenCalled();
	});
});
