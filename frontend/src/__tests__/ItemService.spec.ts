import { describe, it, vi, beforeEach, expect } from "vitest";
import { ItemService } from "../models/Item/ItemService";

const mockResponse = {
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
			description: "Carpaccio de salm\u00e3o defumado",
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
			description: "Torta de lim\u00e3o",
			price: 35,
			category: "Sobremesa",
		},
	],
	count: 8,
};

const mocks = vi.hoisted(() => {
	return {
		request: vi.fn(),
	};
});

vi.mock("../lib/request", () => {
	return {
		request: mocks.request,
	};
});

describe("ItemService", () => {
	let service: ItemService;

	beforeEach(() => {
		service = new ItemService();
		vi.clearAllMocks();
	});

	it("should return a list of items", async () => {
		mocks.request.mockImplementationOnce(() => Promise.resolve(mockResponse));

		const result = await service.getAllItems();

		expect(result).toEqual(mockResponse);
	});
});
