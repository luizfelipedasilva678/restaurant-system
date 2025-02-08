import test from "@playwright/test";
import { tablesBeingAttendedObject } from "./objects/tablesBeingAttendedObject";

test.describe("tablesBeingAttended", () => {
	test("should show the list of tables being attended", async ({ page }) => {
		await tablesBeingAttendedObject.mockSession(page);
		await tablesBeingAttendedObject.mockPaymentMethodRequest(page);
		await tablesBeingAttendedObject.mockItemsRequest(page);
		await tablesBeingAttendedObject.mockOrderRequest(page);
		await tablesBeingAttendedObject.mockTablesRequest(page);

		await tablesBeingAttendedObject.gotToPage(page);

		await tablesBeingAttendedObject.verifyResultPage(page);
	});

	test("should show order creation modal on click", async ({ page }) => {
		await tablesBeingAttendedObject.mockSession(page);
		await tablesBeingAttendedObject.mockItemsRequest(page);
		await tablesBeingAttendedObject.mockPaymentMethodRequest(page);
		await tablesBeingAttendedObject.mockOrderRequest(page);
		await tablesBeingAttendedObject.mockTablesRequest(page);

		await tablesBeingAttendedObject.gotToPage(page);

		await tablesBeingAttendedObject.verifyIfModalToCreateOrderIsVisibleOnClick(
			page,
		);
	});

	test("should show consumption modal on click", async ({ page }) => {
		await tablesBeingAttendedObject.mockSession(page);
		await tablesBeingAttendedObject.mockItemsRequest(page);
		await tablesBeingAttendedObject.mockPaymentMethodRequest(page);
		await tablesBeingAttendedObject.mockOrderRequest(page);
		await tablesBeingAttendedObject.mockTablesRequest(page);

		await tablesBeingAttendedObject.gotToPage(page);

		await tablesBeingAttendedObject.verifyIfModalToAddConsumptionIsVisibleOnClick(
			page,
		);
	});

	test("should finish a order correctly", async ({ page }) => {
		await tablesBeingAttendedObject.mockSession(page);
		await tablesBeingAttendedObject.mockItemsRequest(page);
		await tablesBeingAttendedObject.mockPaymentMethodRequest(page);
		await tablesBeingAttendedObject.mockOrderRequest(page);
		await tablesBeingAttendedObject.mockTablesRequest(page);
		await tablesBeingAttendedObject.mockFulfillRequest(page);

		await tablesBeingAttendedObject.gotToPage(page);

		await tablesBeingAttendedObject.finishOrder(page);
	});

	test("should show a feedback when something unexpected happens", async ({
		page,
	}) => {
		await tablesBeingAttendedObject.mockSession(page);
		await tablesBeingAttendedObject.mockItemsRequest(page);
		await tablesBeingAttendedObject.mockPaymentMethodRequest(page);
		await tablesBeingAttendedObject.mockOrderRequest(page);
		await tablesBeingAttendedObject.mockTablesRequest(page);
		await tablesBeingAttendedObject.mockFulfillRequestWithError(page);

		await tablesBeingAttendedObject.gotToPage(page);

		await tablesBeingAttendedObject.finishOrderWithError(page);
	});
});
