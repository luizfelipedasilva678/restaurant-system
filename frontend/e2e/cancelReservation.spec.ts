import { test } from "@playwright/test";
import { cancelReservationObject } from "./objects/cancelReservationObject";

test.describe("cancelReservation", () => {
  test("should cancel a reservation correctly", async ({ page }) => {
    await cancelReservationObject.fixTime(page);

    await cancelReservationObject.mockSession(page);
    
    await cancelReservationObject.mockReservations(page);
    await cancelReservationObject.mockReservationSuccessResponse(page);

    await cancelReservationObject.goToListingPage(page);
 
    await cancelReservationObject.clickOnFirstCancelButton(page);
    await cancelReservationObject.confirmTheFirstCancel(page);
     
    await cancelReservationObject.mockReservations(page);
    await cancelReservationObject.mockReservationSuccessResponse(page);

    await cancelReservationObject.verifySuccessToastFeedback(page);
  });

  test("should show a feedback when something unexpected happens", async ({
    page,
  }) => {
    await cancelReservationObject.fixTime(page);

    await cancelReservationObject.mockSession(page);
    await cancelReservationObject.mockReservations(page);
    await cancelReservationObject.mockReservationErrorResponse(page);

    await cancelReservationObject.goToListingPage(page);
    await cancelReservationObject.clickOnFirstCancelButton(page);
    await cancelReservationObject.confirmTheFirstCancel(page);

    await cancelReservationObject.verifyErrorToastFeedback(page);
  });
});
