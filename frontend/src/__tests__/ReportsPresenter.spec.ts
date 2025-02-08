import { beforeEach, describe, expect, it, vi } from "vitest";
import { ReportsPresenter } from "../presenters/ReportsPresenter";

const mocks = vi.hoisted(() => {
	const onError = vi.fn();
	const onSuccess = vi.fn();
	const showSalesByCategory = vi.fn();
	const showSalesByDay = vi.fn();
	const showSalesByEmployee = vi.fn();
	const showSalesByPaymentMethod = vi.fn();
	const getReportByCategory = vi.fn();
	const getReportByDay = vi.fn();
	const getReportByEmployee = vi.fn();
	const getReportByPaymentMethod = vi.fn();

	return {
		onError,
		onSuccess,
		showSalesByCategory,
		showSalesByDay,
		showSalesByEmployee,
		showSalesByPaymentMethod,
		getReportByCategory,
		getReportByDay,
		getReportByEmployee,
		getReportByPaymentMethod,
		viewMock: {
			onError,
			onSuccess,
			showSalesByCategory,
			showSalesByDay,
			showSalesByEmployee,
			showSalesByPaymentMethod,
		},
		reportsService: vi.fn(() => ({
			getReportByCategory,
			getReportByDay,
			getReportByEmployee,
			getReportByPaymentMethod,
		})),
	};
});

vi.mock("../models/Reports/ReportService", () => ({
	ReportsService: mocks.reportsService,
}));

describe("ReportsPresenter", () => {
	let presenter: ReportsPresenter;

	beforeEach(() => {
		presenter = new ReportsPresenter(mocks.viewMock);
		vi.clearAllMocks();
	});

	it("should return the data to show the sales by category report", async () => {
		await presenter.getReportByCategory("2024-12-07", "2024-12-07");

		expect(mocks.showSalesByCategory).toHaveBeenCalled();
	});

	it("should return the data to show the sales by payment method report", async () => {
		await presenter.getReportByPaymentMethod("2024-12-07", "2024-12-07");

		expect(mocks.showSalesByPaymentMethod).toHaveBeenCalled();
	});

	it("should return the data to show the sales by day report", async () => {
		await presenter.getReportByDay("2024-12-07", "2024-12-07");

		expect(mocks.showSalesByDay).toHaveBeenCalled();
	});

	it("should return the data to show the sales by employee report", async () => {
		await presenter.getReportByEmployee("2024-12-07", "2024-12-07");

		expect(mocks.showSalesByEmployee).toHaveBeenCalled();
	});
});
