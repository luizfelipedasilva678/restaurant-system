import { type Page, expect } from "@playwright/test";

export const cancelReservationObject = {
  async fixTime(page: Page) {
    await page.clock.setFixedTime(new Date("2024-12-07T08:00:00"));
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
              type: "attendant",
            },
          },
        },
        status: 200,
      });
    });
  },
  async mockReservations(page: Page) {
    await page.route(
      /\/api\/v1\/reservations\?page=1&perPage=5$/g,
      async (route) => {
        await route.fulfill({
          json: {
            count: 1,
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
            ],
          },
          status: 200,
        });
      }
    );
  },
  async mockReservationSuccessResponse(page: Page) {
    await page.route(/\/api\/v1\/reservations\/\d+$/g, async (route) => {
      await route.fulfill({
        json: {},
        status: 200,
      });
    });
  },
  async mockReservationErrorResponse(page: Page) {
    await page.route(/\/api\/v1\/reservations\/\d+$/g, async (route) => {
      await route.fulfill({
        json: {},
        status: 500,
      });
    });
  },
  async goToListingPage(page: Page) {
    await page.goto("http://localhost:5173/");
  },
  async clickOnFirstCancelButton(page: Page) {
    await page.locator("[data-testid='cancel-trigger-1']").click();
  },
  async confirmTheFirstCancel(page: Page) {
    await page.locator("[data-testid='cancel-reservation-trigger-1']").click();
  },
  async verifySuccessToastFeedback(page: Page) {
    expect(await page.locator(".toastify").innerText()).toBe(
      "Reserva cancelada com sucesso."
    );
  },
  async verifyErrorToastFeedback(page: Page) {
    expect(await page.locator(".toastify").innerText()).toBe(
      "Erro ao executar tarefa"
    );
  },
};
