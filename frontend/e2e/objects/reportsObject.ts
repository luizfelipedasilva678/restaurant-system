import { type Page, expect } from "@playwright/test";

export const reportsObject = {
  async mockSession(page: Page) {
    await page.route(/\/api\/v1\/auth\/session$/g, async (route) => {
      await route.fulfill({
        json: {
          session: {
            user: {
              id: 1,
              name: "test",
              login: "test",
              type: "manager",
            },
          },
        },
        status: 200,
      });
    });
  },
  async mockCategoryReportRequest(page: Page) {
    await page.route(
      /.*\/api\/v1\/reports\/sales-by-category.*/g,
      async (route) => {
        await route.fulfill({
          json: [
            {
              category: "Entrada",
              sales: 100,
            },
          ],
          status: 200,
        });
      }
    );
  },
  async mockDayReportRequest(page: Page) {
    await page.route(/.*\/api\/v1\/reports\/sales-by-day.*/g, async (route) => {
      await route.fulfill({
        json: [
          {
            date: "2024-12-06",
            sales: 100,
          },
        ],
        status: 200,
      });
    });
  },
  async mockPaymentMethodReportRequest(page: Page) {
    await page.route(
      /.*\/api\/v1\/reports\/sales-by-payment-method.*/g,
      async (route) => {
        await route.fulfill({
          json: [
            {
              payment_method: "Cartão de crédito",
              sales: 100,
            },
          ],
          status: 200,
        });
      }
    );
  },
  async mockEmployeeReportRequest(page: Page) {
    await page.route(
      /.*\/api\/v1\/reports\/sales-by-employee.*/g,
      async (route) => {
        await route.fulfill({
          json: [
            {
              employee: "employee one",
              sales: 100,
            },
          ],
          status: 200,
        });
      }
    );
  },
  async goToCategoryReportPage(page: Page) {
    await page.goto("http://localhost:5173/sales-by-category");
  },
  async goToDayReportPage(page: Page) {
    await page.goto("http://localhost:5173/sales-by-date");
  },
  async goToPaymentMethodReportPage(page: Page) {
    await page.goto("http://localhost:5173/sales-by-payment-method");
  },
  async goToEmployeeReportPage(page: Page) {
    await page.goto("http://localhost:5173/sales-by-employee");
  },
  async clickOnReportButton(page: Page) {
    await page.locator("[data-testid='get-report-button']").click();
  },
  async verifyReportResult(page: Page) {
    expect(
      await page.locator("#report-container").getAttribute("width")
    ).not.toBeNull();
    expect(
      await page.locator("#report-container").getAttribute("height")
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
      "Os campos devem ser preenchidos corretamente. A data inicial deve ser menor que a data final."
    );
  },
};
