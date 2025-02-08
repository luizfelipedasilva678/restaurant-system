import { beforeEach, describe, expect, it, vi } from "vitest";
import { TableService } from "../models/Table/TableService";

let url = "";

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

describe("TableService", () => {
	let service: TableService;

	beforeEach(() => {
		service = new TableService();
	});

	it("should return a list tables", async () => {
		const mockResponse = [{ id: 1, number: 1 }];

		mocks.request.mockImplementationOnce(() => Promise.resolve(mockResponse));

		const result = await service.findAll();

		expect(result).toEqual(mockResponse);
	});

	it("should set the start date correctly", async () => {
		const startDate = "2023-01-01";
		const mockResponse = [{ id: 1, number: 1 }];

		mocks.request.mockImplementationOnce((u: string) => {
			url = u;
			return Promise.resolve(mockResponse);
		});

		const result = await service.findAll(startDate);

		expect(url.includes(startDate)).toBe(true);
		expect(result).toEqual(mockResponse);
	});

	it("should return the tables paginated correctly", async () => {
		const mockResponse = [
			{ id: 1, number: 1 },
			{ id: 2, number: 2 },
			{ id: 3, number: 3 },
			{ id: 4, number: 4 },
			{ id: 5, number: 5 },
		];

		mocks.request.mockImplementation(() => Promise.resolve(mockResponse));

		const result = await service.findAll();

		expect(result).toEqual(mockResponse);
	});
});
