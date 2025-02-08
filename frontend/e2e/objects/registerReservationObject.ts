import { type Page, expect } from "@playwright/test";

export const registerReservationObject = {
	async mockSession(page: Page) {
		await page.route(/\/api\/v1\/auth\/session$/g, async (route) => {
			await route.fulfill({
				json: {
					session: {
						user: {
							id: 1,
							name: "test",
							login: "test",
							userType: "attendant",
						},
					},
				},
				status: 200,
			});
		});
	},
	async mockTableSuccessResponse(page: Page) {
		await page.route(/\/api\/v1\/tables\?startDate=.*$/g, async (route) => {
			await route.fulfill({
				json: [],
			});
		});
	},
	async mockTables(page: Page) {
		await page.route(/\/api\/v1\/tables$/g, async (route) => {
			await route.fulfill({
				json: [
					{ id: 1, number: 1 },
					{ id: 2, number: 2 },
				],
			});
		});
	},
	async mockEmployees(page: Page) {
		await page.route(/.*\/api\/v1\/employees.*/g, async (route) => {
			await route.fulfill({
				json: {
					count: 2,
					data: [
						{ id: 1, name: "employee 1" },
						{ id: 2, name: "employee 2" },
					],
				},
			});
		});
	},
	async mockReservationSuccessResponse(page: Page) {
		await page.route(/.*\/api\/v1\/reservations$/g, async (route) => {
			await route.fulfill({ json: {}, status: 200 });
		});
	},
	async mockReservationErrorResponse(page: Page) {
		await page.route(/.*\/api\/v1\/reservations$/g, async (route) => {
			await route.fulfill({ json: { message: "Error" }, status: 500 });
		});
	},
	async mockReservations(page: Page) {
		await page.route(
			/.*\/api\/v1\/reservations\?page=1&perPage=5$/g,
			async (route) => {
				await route.fulfill({
					json: {
						count: 1,
						data: [
							{
								client: { name: "client one", phone: "(22) 2222-2222" },
								employee: { id: 1, name: "employee one" },
								startTime: "2024-12-07 10:00:00",
								endTime: "2024-12-07 12:00:00",
								id: 1,
								status: "active",
								table: { id: 1, number: 1 },
							},
						],
					},
					status: 200,
				});
			},
		);
	},
	async goToReservationPage(page: Page) {
		await page.goto("http://localhost:5173/register/reservation");
	},
	async fillName(page: Page) {
		await page.locator("[data-testid='field-name']").fill("Pedro");
	},
	async fillPhone(page: Page) {
		await page.locator("[data-testid='field-phone']").fill("(22) 2222-2222");
	},
	async fillReservation(page: Page) {
		await page
			.locator("[data-testid='field-reservation']")
			.fill("2024-12-08T18:16", { force: true });
	},
	async blurReservation(page: Page) {
		await page.locator("[data-testid='field-reservation']").blur();
	},
	async selectTableOne(page: Page) {
		await page.locator("[data-testid='table-select']").selectOption("1");
	},
	async selectEmployeeOne(page: Page) {
		await page.locator("[data-testid='employee-select']").selectOption("1");
	},
	async submitCreateReservation(page: Page) {
		await page
			.locator("[data-testid='create-reservation']")
			.click({ force: true });
	},
	async verifySuccessFeedback(page: Page) {
		expect(await page.locator(".toastify").innerText()).toBe(
			"Reserva feita com sucesso.",
		);
	},
	async verifyErrorFeedback(page: Page) {
		expect(await page.locator(".toastify").innerText()).toBe(
			"Erro ao executar tarefa",
		);
	},
};
