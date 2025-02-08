import { beforeEach, describe, expect, it, vi } from "vitest";
import { ReportsService } from "../models/Reports/ReportService";
import type {
	SalesByCategoryReport,
	SalesByDayReport,
	SalesByEmployeeReport,
	SalesByPaymentMethodReport,
} from "../models/Reports/Reports";

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
	return {
		request: vi.fn(),
	};
});

vi.mock("../lib/request", () => {
	return {
		request: mocks.request,
	};
});

describe("ReportsService", () => {
	let service: ReportsService;

	beforeEach(() => {
		service = new ReportsService();
		vi.clearAllMocks();
	});

	it("should return an empty array when something goes wrong", async () => {
		mocks.request.mockRejectedValue(new Error("error"));
		const data2 = await service.getReportByEmployee("2024-12-07", "2024-12-07");
		const data3 = await service.getReportByCategory("2024-12-07", "2024-12-07");
		const data4 = await service.getReportByDay("2024-12-07", "2024-12-07");
		const data1 = await service.getReportByPaymentMethod(
			"2024-12-07",
			"2024-12-07",
		);

		expect(data1).toEqual([]);
		expect(data2).toEqual([]);
		expect(data3).toEqual([]);
		expect(data4).toEqual([]);
	});

	it("should the date to build the sales by day report correctly", async () => {
		mocks.request.mockResolvedValueOnce(mockSalesByDayReport);

		const data = await service.getReportByPaymentMethod(
			"2024-12-07",
			"2024-12-07",
		);

		expect(data).toEqual(mockSalesByDayReport);
	});

	it("should the date to build the sales by payment method report correctly", async () => {
		mocks.request.mockResolvedValueOnce(mockSalesByPaymentMethod);

		const data = await service.getReportByPaymentMethod(
			"2024-12-07",
			"2024-12-07",
		);

		expect(data).toEqual(mockSalesByPaymentMethod);
	});

	it("should the date to build the sales by employee report correctly", async () => {
		mocks.request.mockResolvedValueOnce(mockSalesByEmployeeReport);

		const data = await service.getReportByEmployee("2024-12-07", "2024-12-07");

		expect(data).toEqual(mockSalesByEmployeeReport);
	});

	it("should the date to build the sales by category report correctly", async () => {
		mocks.request.mockResolvedValueOnce(mockSalesByCategoryReport);

		const data = await service.getReportByCategory("2024-12-07", "2024-12-07");

		expect(data).toEqual(mockSalesByCategoryReport);
	});
});
