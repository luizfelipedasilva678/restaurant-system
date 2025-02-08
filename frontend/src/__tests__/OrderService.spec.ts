import { beforeEach, describe, expect, it, vi } from "vitest";
import { OrderService } from "../models/Order/OrderService";
import type {
	AddItemsDTO,
	Order,
	OrderCreationDTO,
} from "../models/Order/Order";

const orders: Order[] = [
	{
		id: 9,
		status: "open",
		items: [],
		client: {
			id: 3,
			name: "João",
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
			name: "Pedro",
		},
		table: {
			id: 2,
			number: 2,
		},
	},
];

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

describe("Order Service", () => {
	let service: OrderService;

	beforeEach(() => {
		service = new OrderService();
		vi.clearAllMocks();
	});

	it("should return a list of orders correctly", async () => {
		mocks.request.mockImplementationOnce(() => Promise.resolve(orders));

		const result = await service.getOrders();

		expect(result).toEqual(orders);
	});

	it("should return order correctly", async () => {
		mocks.request.mockImplementationOnce((url: string) => {
			const id = url.match(/\/orders\/(\d+)/)?.[1] ?? 0;
			return Promise.resolve(orders.find((order) => order.id === +id) ?? null);
		});

		const result = await service.getOrder(9);

		expect(result).toEqual(orders.find((order) => order.id === 9));
	});

	it("should return an empty array when something goes wrong", async () => {
		mocks.request.mockImplementation((url: string) => {
			throw new Error("Order not found");
		});

		const result = await service.getOrders();

		expect(result).toEqual([]);
	});

	it("should return null when something goes wrong", async () => {
		mocks.request.mockImplementation((url: string) => {
			throw new Error("Order not found");
		});

		const result = await service.getOrder(99);

		expect(result).toBeNull();
	});

	it("should create an order correctly", async () => {
		mocks.request.mockImplementation((_: string, init?: RequestInit) => {
			const orderCreationDTO = JSON.parse(
				init?.body as string,
			) as OrderCreationDTO;

			orders.push({
				id: 11,
				status: "open",
				client: {
					id: 5,
					name: orderCreationDTO.clientName,
				},
				table: {
					id: 5,
					number: 5,
				},
				items: [],
			});

			return Promise.resolve({
				message: "Pedido criado com sucesso",
			});
		});

		const result = await service.createOrder({
			clientName: "Joaquim",
			tableId: 5,
		});

		expect(result).toEqual({
			message: "Pedido criado com sucesso",
		});
		expect(orders.length).toBe(3);
	});

	it("should add items to the order correctly", async () => {
		mocks.request.mockImplementationOnce((url: string, init?: RequestInit) => {
			const id = url.match(/\/orders\/(\d+)/)?.[1] ?? 0;
			const addItemsToOrderDto = JSON.parse(
				init?.body as string,
			) as AddItemsDTO;

			const order = orders.find((order) => order.id === +id);

			if (order) {
				order.items = [
					...order.items,
					...addItemsToOrderDto.items.map((item) => ({
						id: 1,
						itemId: item.itemId,
						quantity: item.quantity,
						category: "",
						description: "",
						price: 10,
					})),
				];
			}

			return Promise.resolve({
				message: "Itens adicionados com sucesso",
			});
		});

		const result = await service.addItems({
			orderId: 9,
			items: [
				{
					itemId: 1,
					quantity: 1,
				},
			],
		});

		expect(result).toEqual({
			message: "Itens adicionados com sucesso",
		});
		expect(orders.find((order) => order.id === 9)?.items.length).toBe(1);
	});
});
