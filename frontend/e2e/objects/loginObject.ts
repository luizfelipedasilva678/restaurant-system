import { type Page, expect } from "@playwright/test";

export const loginObject = {
  async mockLoginSuccessResponse(page: Page) {
    await page.route(/\/api\/v1\/auth\/login$/g, async (route) => {
      await route.fulfill({
        json: {
          message: "Sessão iniada com sucesso",
        },
        status: 200,
      });
    });
  },
  async mockLoginBadRequestResponse(page: Page) {
    await page.route(/\/api\/v1\/auth\/login$/g, async (route) => {
      await route.fulfill({
        json: {},
        status: 400,
      });
    });
  },
  async mockLoginErrorResponse(page: Page) {
    await page.route(/\/api\/v1\/auth\/login$/g, async (route) => {
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
  async mockErrorSession(page: Page) {
    await page.route(/\/api\/v1\/auth\/session$/g, async (route) => {
      await route.fulfill({
        json: {},
        status: 401,
      });
    });
  },
  async goToLoginPage(page: Page) {
    await page.goto("http://localhost:5173/login", {
      waitUntil: "load",
    });
  },
  async fillLoginInput(page: Page) {
    await page.locator("[data-testid='login-field']").fill("user1");
  },
  async fillPasswordInput(page: Page) {
    await page.locator("[data-testid='password-field']").fill("123");
  },
  async submitLogin(page: Page) {
    await page.locator("[data-testid='login-button']").click();
  },
  async verifySuccessLoginFeedback(page: Page) {
    const locator =  page.getByText("Login realizado com sucesso");

    await locator.waitFor({
      state: "visible"
    })

    expect(await locator.isVisible()).toBe(
      true
    );
  },
  async verifyBadRequestLoginFeedback(page: Page) {
    expect(await page.locator(".toastify").innerText()).toBe(
      "Usuário ou senha inválidos"
    );
  },
  async verifyErrorLoginFeedback(page: Page) {
    expect(await page.locator(".toastify").innerText()).toBe(
      "Erro ao autenticar usuário"
    );
  },
};
