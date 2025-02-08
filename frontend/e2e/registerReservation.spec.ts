import { test } from "@playwright/test";
import { registerReservationObject } from "./objects/registerReservationObject";

test.describe("registerReservation", () => {
	test("should register a new reservation correctly", async ({ page }) => {
		await registerReservationObject.mockSession(page);
		await registerReservationObject.mockTableSuccessResponse(page);
		await registerReservationObject.mockTables(page);
		await registerReservationObject.mockEmployees(page);
		await registerReservationObject.mockReservationSuccessResponse(page);
		await registerReservationObject.mockReservations(page);

		await registerReservationObject.goToReservationPage(page);
		await registerReservationObject.fillName(page);
		await registerReservationObject.fillPhone(page);
		await registerReservationObject.fillReservation(page);
		await registerReservationObject.blurReservation(page);

		await registerReservationObject.selectTableOne(page);
		await registerReservationObject.selectEmployeeOne(page);
		await registerReservationObject.submitCreateReservation(page);

		await registerReservationObject.verifySuccessFeedback(page);
	});

	test("should show a feedback when something unexpected happens", async ({
		page,
	}) => {
		await registerReservationObject.mockSession(page);
		await registerReservationObject.mockTableSuccessResponse(page);
		await registerReservationObject.mockTables(page);
		await registerReservationObject.mockEmployees(page);
		await registerReservationObject.mockReservationErrorResponse(page);
		await registerReservationObject.mockReservations(page);

		await registerReservationObject.goToReservationPage(page);
		await registerReservationObject.fillName(page);
		await registerReservationObject.fillPhone(page);
		await registerReservationObject.fillReservation(page);
		await registerReservationObject.blurReservation(page);

		await registerReservationObject.selectTableOne(page);
		await registerReservationObject.selectEmployeeOne(page);
		await registerReservationObject.submitCreateReservation(page);

		await registerReservationObject.verifyErrorFeedback(page);
	});
});
