import { beforeEach, describe, expect, it, vi } from "vitest";
import { PaymentMethodService } from "../models/PaymentMethod/PaymentMethodService";

describe("PaymentMethodService", () => {
	let service: PaymentMethodService;

	beforeEach(() => {
		service = new PaymentMethodService();
	});

	it("should return a list of payments methods", async () => {
		const mockResponse = [
			{ id: 1, name: "Pix" },
			{ id: 2, name: "Cartão de crédito" },
		];

		vi.spyOn(service, "findAll").mockImplementationOnce(() =>
			Promise.resolve(mockResponse),
		);

		const result = await service.findAll();

		expect(result).toEqual(mockResponse);
	});
});
