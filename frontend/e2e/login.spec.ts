import test from "@playwright/test";
import { loginObject } from "./objects/loginObject";

test.describe("login", () => {
  test("should login correctly", async ({ page }) => {
    await loginObject.mockLoginSuccessResponse(page);
    await loginObject.mockErrorSession(page);

    await loginObject.goToLoginPage(page);

    await loginObject.mockSession(page);

    await loginObject.fillLoginInput(page);
    await loginObject.fillPasswordInput(page);
    await loginObject.submitLogin(page);

    await loginObject.verifySuccessLoginFeedback(page);
  });

  test("should show the message 'Usuário ou senha inválidos' when the user credentials are invalid", async ({
    page,
  }) => {
    await loginObject.mockLoginBadRequestResponse(page);
    await loginObject.mockErrorSession(page);

    await loginObject.goToLoginPage(page);

    await loginObject.fillLoginInput(page);
    await loginObject.fillPasswordInput(page);
    await loginObject.submitLogin(page);

    await loginObject.verifyBadRequestLoginFeedback(page);
  });

  test("should show the message 'Erro ao autenticar usuário' when something goes wrong", async ({
    page,
  }) => {
    await loginObject.mockLoginErrorResponse(page);
    await loginObject.mockErrorSession(page);

    await loginObject.goToLoginPage(page);

    await loginObject.fillLoginInput(page);
    await loginObject.fillPasswordInput(page);
    await loginObject.submitLogin(page);

    await loginObject.verifyErrorLoginFeedback(page);
  });
});
