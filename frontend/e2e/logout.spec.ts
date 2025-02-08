import test from "@playwright/test";
import { logoutObject } from "./objects/logoutObject";

test.describe("logout", () => {
	test("should logout correctly", async ({ page }) => {
		await logoutObject.mockSessionStorage(page);
		await logoutObject.mockReservation(page);
		await logoutObject.mockLogoutSuccess(page);
		await logoutObject.mockSession(page);

		await logoutObject.goToIndex(page);
		await logoutObject.clickOnLogoutButton(page);

		await logoutObject.waitRedirect(page);

		await logoutObject.verifyPageUrl(page);
	});

	test("should should the message 'Erro ao finalizar sessão' when logout goes wrong", async ({
		page,
	}) => {
		await logoutObject.mockSessionStorage(page);
		await logoutObject.mockReservation(page);
		await logoutObject.mockLogoutError(page);
		await logoutObject.mockSession(page);

		await logoutObject.goToIndex(page);
		await logoutObject.clickOnLogoutButton(page);

		await logoutObject.verifyToastFeedback(page);
	});
});
