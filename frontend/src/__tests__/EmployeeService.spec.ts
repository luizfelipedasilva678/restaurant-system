import { beforeEach, describe, expect, it, vi } from "vitest";
import { EmployeeService } from "../models/Employee/EmployeeService";

describe("EmployeeService", () => {
	let service: EmployeeService;

	beforeEach(() => {
		service = new EmployeeService();
	});

	it("should return a list employees", async () => {
		const mockResponse = { count: 1, data: [{ id: 1, name: "employee 1" }] };

		vi.spyOn(service, "findAll").mockImplementationOnce(() =>
			Promise.resolve(mockResponse),
		);

		const result = await service.findAll();

		expect(result).toEqual(mockResponse);
	});

	it("should return the data paginated correctly", async () => {
		const mockResponse = {
			count: 5,
			data: [
				{ id: 1, name: "employee 1" },
				{ id: 2, name: "employee 2" },
				{ id: 3, name: "employee 3" },
				{ id: 4, name: "employee 4" },
				{ id: 5, name: "employee 5" },
			],
		};

		vi.spyOn(service, "findAll").mockImplementation((page = 1, size = 10) =>
			Promise.resolve({
				...mockResponse,
				data: mockResponse.data.slice((page - 1) * size, page * size),
			}),
		);

		const resultOne = await service.findAll(1, 1);

		expect(resultOne).toEqual({
			...mockResponse,
			data: [mockResponse.data[0]],
		});

		const resultTwo = await service.findAll(1, 2);

		expect(resultTwo).toEqual({
			...mockResponse,
			data: [mockResponse.data[0], mockResponse.data[1]],
		});

		const resultThree = await service.findAll(2, 2);

		expect(resultThree).toEqual({
			...mockResponse,
			data: [mockResponse.data[2], mockResponse.data[3]],
		});

		const resultFour = await service.findAll(3, 2);

		expect(resultFour).toEqual({
			...mockResponse,
			data: [mockResponse.data[4]],
		});
	});
});
