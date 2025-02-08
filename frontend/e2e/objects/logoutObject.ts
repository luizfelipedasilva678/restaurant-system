import { type Page, expect } from "@playwright/test";

export const logoutObject = {
	async mockSessionStorage(page: Page) {
		await page.addInitScript(() => {
			window.sessionStorage.setItem(
				"__SESSION__",
				JSON.stringify({
					session: {
						user: {
							id: 1,
							name: "test",
							login: "test",
							userType: "attendant",
						},
					},
				}),
			);
		});
	},
	async mockReservation(page: Page) {
		await page.route(/.*\/api\/v1\/reservations.*/g, async (route) => {
			await route.fulfill({ json: { data: [], count: 0 } });
		});
	},
	async mockLogoutSuccess(page: Page) {
		await page.route(/\/api\/v1\/auth\/logout$/g, async (route) => {
			await route.fulfill({
				json: {
					message: "Sessão destruída com sucesso",
				},
				status: 200,
			});
		});
	},
	async mockLogoutError(page: Page) {
		await page.route(/\/api\/v1\/auth\/logout$/g, async (route) => {
			await route.fulfill({
				json: {},
				status: 500,
			});
		});
	},
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
	async goToIndex(page: Page) {
		await page.goto("http://localhost:5173", {
			waitUntil: "load",
		});
	},
	async clickOnLogoutButton(page: Page) {
		await page.locator("[data-testid='logout-button']").click();
	},
	async waitRedirect(page: Page) {
		await page.waitForURL(/login/);
	},
	async verifyPageUrl(page: Page) {
		expect(page.url()).toContain("/login");
	},
	async verifyToastFeedback(page: Page) {
		expect(await page.locator(".toastify").innerText()).toBe(
			"Erro ao finalizar sessão",
		);
	},
};
