import { test } from "@playwright/test";
import { reservationListObject } from "./objects/reservationListObject";

const mockResponse = {
	count: 5,
	data: [
		{
			client: { name: "client one" },
			employee: { id: 1, name: "employee one" },
			startTime: "2024-12-07 10:00:00",
			endTime: "2024-12-07 12:00:00",
			id: 1,
			status: "active",
			table: { id: 1, number: 1 },
		},
		{
			client: { name: "client two" },
			employee: { id: 2, name: "employee two" },
			startTime: "2024-12-07 10:00:00",
			endTime: "2024-12-07 12:00:00",
			id: 2,
			status: "active",
			table: { id: 2, number: 2 },
		},
		{
			client: { name: "client three" },
			employee: { id: 3, name: "employee three" },
			startTime: "2024-12-07 10:00:00",
			endTime: "2024-12-07 12:00:00",
			id: 3,
			status: "active",
			table: { id: 3, number: 3 },
		},
		{
			client: { name: "client four" },
			employee: { id: 4, name: "employee four" },
			startTime: "2024-12-07 10:00:00",
			endTime: "2024-12-07 12:00:00",
			id: 4,
			status: "active",
			table: { id: 4, number: 4 },
		},
		{
			client: { name: "client five" },
			employee: { id: 5, name: "employee five" },
			startTime: "2024-12-07 10:00:00",
			endTime: "2024-12-07 12:00:00",
			id: 5,
			status: "active",
			table: { id: 5, number: 5 },
		},
	],
};

test.describe("reservationList", () => {
	test("should show 'Nenhuma reserva cadastrada no momento.' message when api returns count equal to 0", async ({
		page,
	}) => {
		await reservationListObject.mockSession(page);
		await reservationListObject.mockReservations(page);

		await reservationListObject.goToIndex(page);

		await reservationListObject.verifyTitle(page);
	});

	test("should show the list of reservations correctly", async ({ page }) => {
		await reservationListObject.mockSession(page);
		await reservationListObject.mockReservationsWith(page, mockResponse);

		await reservationListObject.goToIndex(page);

		await reservationListObject.verifyListVisibility(page);
	});
});
