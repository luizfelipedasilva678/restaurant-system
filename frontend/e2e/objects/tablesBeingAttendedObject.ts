import { type Page, expect } from "@playwright/test";

export const tablesBeingAttendedObject = {
	async mockPaymentMethodRequest(page: Page) {
		await page.route(/\/api\/v1\/payments-methods$/g, async (route) => {
			await route.fulfill({
				json: [
					{
						id: 1,
						name: "Pix",
					},
					{
						id: 2,
						name: "Dinheiro",
					},
					{
						id: 3,
						name: "Cart\u00e3o de cr\u00e9dito",
					},
					{
						id: 4,
						name: "Cart\u00e3o de d\u00e9bito",
					},
				],
				status: 200,
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
							type: "manager",
						},
					},
				},
				status: 200,
			});
		});
	},
	async mockOrderRequest(page: Page) {
		await page.route(/\/api\/v1\/orders$/g, async (route) => {
			await route.fulfill({
				json: [
					{
						id: 8,
						status: "open",
						items: [
							{
								id: 60,
								itemId: 3,
								quantity: 1,
								price: 30,
								description: "Espaguete ao frutos do mar",
								category: "Prato Principal",
							},
							{
								id: 62,
								itemId: 3,
								quantity: 1,
								price: 30,
								description: "Espaguete ao frutos do mar",
								category: "Prato Principal",
							},
							{
								id: 63,
								itemId: 2,
								quantity: 1,
								price: 20,
								description: "Carpaccio de salm\u00e3o defumado",
								category: "Entrada",
							},
							{
								id: 64,
								itemId: 6,
								quantity: 1,
								price: 12,
								description: "Mojito tradicional",
								category: "Bebida",
							},
						],
						client: {
							id: 1,
							name: "Jo\u00e3o",
						},
						table: {
							id: 1,
							number: 1,
						},
					},
					{
						id: 9,
						status: "open",
						items: [],
						client: {
							id: 3,
							name: "",
						},
						table: {
							id: 2,
							number: 2,
						},
					},
					{
						id: 10,
						status: "open",
						items: [],
						client: {
							id: 4,
							name: "ff14324234",
						},
						table: {
							id: 3,
							number: 3,
						},
					},
				],
				status: 200,
			});
		});
	},
	async mockItemsRequest(page: Page) {
		await page.route(/\/api\/v1\/items$/g, async (route) => {
			await route.fulfill({
				json: {
					data: [
						{
							id: 1,
							code: "E1",
							description: "Crostini",
							price: 25,
							category: "Entrada",
						},
						{
							id: 2,
							code: "E2",
							description: "Carpaccio de salmão defumado",
							price: 20,
							category: "Entrada",
						},
						{
							id: 3,
							code: "PP1",
							description: "Espaguete ao frutos do mar",
							price: 30,
							category: "Prato Principal",
						},
						{
							id: 4,
							code: "PP2",
							description: "Lula grelhada com arroz negro",
							price: 35,
							category: "Prato Principal",
						},
						{
							id: 5,
							code: "B1",
							description: "Negroni",
							price: 15,
							category: "Bebida",
						},
						{
							id: 6,
							code: "B2",
							description: "Mojito tradicional",
							price: 12,
							category: "Bebida",
						},
						{
							id: 7,
							code: "S1",
							description: "Pudim de leite condensado",
							price: 23,
							category: "Sobremesa",
						},
						{
							id: 8,
							code: "S2",
							description: "Torta de limão",
							price: 35,
							category: "Sobremesa",
						},
					],
					count: 8,
				},
				status: 200,
			});
		});
	},
	async mockTablesRequest(page: Page) {
		await page.route(/\/api\/v1\/tables$/g, async (route) => {
			await route.fulfill({
				json: [
					{
						id: 1,
						number: 1,
					},
					{
						id: 2,
						number: 2,
					},
					{
						id: 3,
						number: 3,
					},
					{
						id: 4,
						number: 4,
					},
					{
						id: 5,
						number: 5,
					},
					{
						id: 6,
						number: 6,
					},
					{
						id: 7,
						number: 7,
					},
					{
						id: 8,
						number: 8,
					},
					{
						id: 9,
						number: 9,
					},
					{
						id: 10,
						number: 10,
					},
				],
				status: 200,
			});
		});
	},
	async mockFulfillRequest(page: Page) {
		await page.route(/\/api\/v1\/orders\/fulfill$/g, async (route) => {
			await route.fulfill({
				json: {
					message: "pedido finalizado com sucesso",
				},
				status: 200,
			});
		});
	},
	async mockFulfillRequestWithError(page: Page) {
		await page.route(/\/api\/v1\/orders\/fulfill$/g, async (route) => {
			await route.fulfill({
				json: {},
				status: 500,
			});
		});
	},
	async gotToPage(page: Page) {
		await page.goto("http://localhost:5173/tables-being-attended");
	},
	async verifyResultPage(page: Page) {
		const locator = page.locator("#tables-being-attended");

		await locator.waitFor({
			state: "visible",
		});

		expect(await page.locator("h3.title").innerText()).toBe(
			"Mesas sendo atendidas",
		);

		expect(
			await page.getByTestId("tables-being-attended-list").innerHTML(),
		).toBeTruthy();
	},
	async verifyIfModalToCreateOrderIsVisibleOnClick(page: Page) {
		const locator = page.locator("#tables-being-attended");

		await locator.waitFor({
			state: "visible",
		});

		await page.locator("#create-order-button").click();

		expect(
			await page.locator("#order-creation-modal.is-active").isVisible(),
		).toBeTruthy();
	},
	async verifyIfModalToAddConsumptionIsVisibleOnClick(page: Page) {
		const locator = page.locator("#tables-being-attended");

		locator.waitFor({
			state: "visible",
		});

		await page
			.locator("button[data-order-id='8']", {
				hasText: "Lançar consumos",
			})
			.click();

		expect(
			await page.locator("#consumptions-modal.is-active").isVisible(),
		).toBeTruthy();
	},
	async finishOrder(page: Page) {
		await page.locator("#tables-being-attended").waitFor({
			state: "visible",
		});

		await page.locator("[fulfill-order='true'][data-order-id='8']").click();

		await page.locator("[id='order-fulfill-form'][data-order-id='8']").waitFor({
			state: "visible",
		});

		await page.locator("[id='payment-method-id']").selectOption("2");

		await page.locator("[id='discount-percentage']").fill("0");

		await page.locator("[id='fulfill-order']").click();

		await expect(page.getByText("Pedido finalizado com sucesso")).toBeVisible();
	},
	async finishOrderWithError(page: Page) {
		await page.locator("#tables-being-attended").waitFor({
			state: "visible",
		});

		await page.locator("[fulfill-order='true'][data-order-id='8']").click();

		await page.locator("[id='order-fulfill-form'][data-order-id='8']").waitFor({
			state: "visible",
		});

		await page.locator("[id='payment-method-id']").selectOption("2");

		await page.locator("[id='discount-percentage']").fill("0");

		await page.locator("[id='fulfill-order']").click();

		await expect(page.getByText("Erro ao finalizar pedido")).toBeVisible();
	},
};
