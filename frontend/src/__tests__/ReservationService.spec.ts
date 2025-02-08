import { beforeEach, describe, expect, it, vi } from "vitest";
import type { Reservation, Status } from "../models/Reservation/Reservation";
import { ReservationService } from "../models/Reservation/ReservationService";

const mockResponse = {
	count: 5,
	data: [
		{
			client: { name: "client one", id: 1 },
			employee: { id: 1, name: "employee one" },
			startTime: "2024-12-07 10:00:00",
			endTime: "2024-12-07 12:00:00",
			id: 1,
			status: "active" as Status,
			table: { id: 1, number: 1 },
		},
		{
			client: { name: "client two", id: 2 },
			employee: { id: 2, name: "employee two" },
			startTime: "2024-12-07 10:00:00",
			endTime: "2024-12-07 12:00:00",
			id: 2,
			status: "active" as Status,
			table: { id: 2, number: 2 },
		},
		{
			client: { name: "client three", id: 3 },
			employee: { id: 3, name: "employee three" },
			startTime: "2024-12-07 10:00:00",
			endTime: "2024-12-07 12:00:00",
			id: 3,
			status: "active" as Status,
			table: { id: 3, number: 3 },
		},
		{
			client: { name: "client four", id: 4 },
			employee: { id: 4, name: "employee four" },
			startTime: "2024-12-07 10:00:00",
			endTime: "2024-12-07 12:00:00",
			id: 4,
			status: "active" as Status,
			table: { id: 4, number: 4 },
		},
		{
			client: { name: "client five", id: 5 },
			employee: { id: 5, name: "employee five" },
			startTime: "2024-12-07 10:00:00",
			endTime: "2024-12-07 12:00:00",
			id: 5,
			status: "active" as Status,
			table: { id: 5, number: 5 },
		},
	],
};

const reservationMock = mockResponse.data[0];

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

describe("ReservationService", () => {
	let service: ReservationService;

	beforeEach(() => {
		service = new ReservationService();
		vi.clearAllMocks();
	});

	it("should return a list of reservations", async () => {
		vi.spyOn(service, "findAll").mockImplementationOnce(() =>
			Promise.resolve(mockResponse),
		);

		const result = await service.findAll();

		expect(result).toEqual(mockResponse);
	});

	it("should return the tables paginated correctly", async () => {
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

	it("should cancel a reservation correctly", async () => {
		const reservation = { ...reservationMock };

		vi.spyOn(service, "cancelReservation").mockImplementation((id) => {
			expect(id).toBe("1");

			reservation.status = "inactive";

			return Promise.resolve();
		});

		await service.cancelReservation("1");

		expect(reservation.status).toEqual("inactive");
	});

	it("should create a reservation correctly", async () => {
		const reservationDTO = {
			clientPhone: "(22) 2222-2222",
			clientName: "client one",
			employeeId: 1,
			startTime: "2024-12-07 10:00:00",
			tableId: 1,
		};

		const reservations: Reservation[] = [];

		vi.spyOn(service, "create").mockImplementation((dto) => {
			expect(dto).toEqual(reservationDTO);

			reservations.push(reservationMock);

			return Promise.resolve(reservationMock);
		});

		const createdReservation = await service.create({
			clientPhone: "(22) 2222-2222",
			clientName: "client one",
			employeeId: 1,
			startTime: "2024-12-07 10:00:00",
			tableId: 1,
		});

		expect(createdReservation).toEqual(reservationMock);
		expect(reservations).toContain(reservationMock);
	});

	it("should return an empty object if no reservations are found", async () => {
		mocks.request.mockResolvedValueOnce({ data: [], count: 0 });

		const data = await service.getReportData("2024-12-07", "2024-12-07");

		expect(JSON.stringify(data)).toBe("{}");
	});

	it("should return the report data correctly", async () => {
		mocks.request.mockResolvedValueOnce(mockResponse);

		const data = await service.getReportData("2024-12-07", "2024-12-07");

		expect(JSON.stringify(data)).toBe(JSON.stringify({ "2024-12-07": 5 }));
	});

	it("should return an empty object given that an error occurs", async () => {
		mocks.request.mockResolvedValueOnce({ count: 0 });

		const data = await service.getReportData("2024-12-07", "2024-12-07");

		expect(data).toEqual({});
	});

	it("should the data correctly when paginated", async () => {
		mocks.request.mockResolvedValue({ ...mockResponse, count: 10001 });

		const data = await service.getReportData("2024-12-07", "2024-12-07");

		expect(JSON.stringify(data)).toBe('{"2024-12-07":10}');
	});
});
