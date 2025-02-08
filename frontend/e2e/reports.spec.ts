import test from "@playwright/test";
import { reportsObject } from "./objects/reportsObject";

test.describe("reports", () => {
  test("should show category report correctly", async ({ page }) => {
    await reportsObject.mockSession(page);
    await reportsObject.mockCategoryReportRequest(page);

    await reportsObject.goToCategoryReportPage(page);

    await reportsObject.clickOnReportButton(page);

    await reportsObject.verifyReportResult(page);
  });

  test("should show employee report correctly", async ({ page }) => {
    await reportsObject.mockSession(page);
    await reportsObject.mockEmployeeReportRequest(page);

    await reportsObject.goToEmployeeReportPage(page);

    await reportsObject.clickOnReportButton(page);

    await reportsObject.verifyReportResult(page);
  });

  test("should show payment method report correctly", async ({ page }) => {
    await reportsObject.mockSession(page);
    await reportsObject.mockPaymentMethodReportRequest(page);

    await reportsObject.goToPaymentMethodReportPage(page);

    await reportsObject.clickOnReportButton(page);

    await reportsObject.verifyReportResult(page);
  });

  test("should show day report correctly", async ({ page }) => {
    await reportsObject.mockSession(page);
    await reportsObject.mockDayReportRequest(page);

    await reportsObject.goToDayReportPage(page);

    await reportsObject.clickOnReportButton(page);

    await reportsObject.verifyReportResult(page);
  });
});
