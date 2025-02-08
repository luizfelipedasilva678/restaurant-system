import { type Page, expect } from "@playwright/test";

export const reservationsReportObject = {
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
	async mockReservationsWith(page: Page, data: unknown) {
		await page.route(/.*\/api\/v1\/reservations.*/g, async (route) => {
			await route.fulfill({ json: data, status: 200 });
		});
	},
	async goToReportPage(page: Page) {
		await page.goto("http://localhost:5173/reservations/report");
	},
	async clickOnReportButton(page: Page) {
		await page.locator("[data-testid='get-report-button']").click();
	},
	async verifyReportResult(page: Page) {
		expect(
			await page.locator("#report-container").getAttribute("width"),
		).not.toBeNull();
		expect(
			await page.locator("#report-container").getAttribute("height"),
		).not.toBeNull();
	},
	async fillInitialDate(page: Page) {
		await page.locator("[data-testid='initial-date']").fill("2024-12-09");
	},
	async fillFinalDate(page: Page) {
		await page.locator("[data-testid='final-date']").fill("2024-12-08");
	},
	async verifyFeedbackToast(page: Page) {
		expect(await page.locator(".toastify").innerText()).toBe(
			"Os campos devem ser preenchidos corretamente. A data inicial deve ser menor que a data final.",
		);
	},
};
