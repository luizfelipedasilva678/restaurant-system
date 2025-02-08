import { type Page, expect } from "@playwright/test";

export const reservationListObject = {
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
	async mockReservations(page: Page) {
		await page.route(/.*\/api\/v1\/reservations.*/g, async (route) => {
			await route.fulfill({ json: { data: [], count: 0 } });
		});
	},
	async goToIndex(page: Page) {
		await page.goto("http://localhost:5173/");
	},
	async verifyTitle(page: Page) {
		await expect(page.locator("[data-testid='title']")).toHaveText(
			"Nenhuma reserva cadastrada no momento.",
		);
	},
	async mockReservationsWith(page: Page, data: unknown) {
		await page.route(/.*\/api\/v1\/reservations.*/g, async (route) => {
			await route.fulfill({ json: data, status: 200 });
		});
	},
	async verifyListVisibility(page: Page) {
		const list = page.locator("[data-testid='list']");
		const card1 = page.locator("[data-testid='card-1']");
		const card2 = page.locator("[data-testid='card-2']");
		const card3 = page.locator("[data-testid='card-3']");

		await list.waitFor({
			state: "visible",
		});

		await card1.waitFor({
			state: "visible",
		});

		await card2.waitFor({
			state: "visible",
		});

		await card3.waitFor({
			state: "visible",
		});

		expect(await list.isVisible()).toBeTruthy();
		expect(await card1.isVisible()).toBeTruthy();
		expect(await card2.isVisible()).toBeTruthy();
		expect(await card3.isVisible()).toBeTruthy();
	},
};
