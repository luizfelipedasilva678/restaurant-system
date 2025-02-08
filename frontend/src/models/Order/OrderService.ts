import { BASE_API_URL } from "../../constants";
import { request } from "../../lib/request";
import type {
	AddItemsDTO,
	FulFillOrderDTO,
	Order,
	OrderCreationDTO,
} from "./Order";

export class OrderService {
	async getOrder(id: number) {
		try {
			const order = await request<Order>(`${BASE_API_URL}/orders/${id}`);

			return order;
		} catch (err) {
			return null;
		}
	}

	async getOrders() {
		try {
			return await request<Order[]>(`${BASE_API_URL}/orders`);
		} catch (err) {
			return [];
		}
	}

	async fulfillOrder(dto: FulFillOrderDTO) {
		return await request<Order>(`${BASE_API_URL}/orders/fulfill`, {
			method: "POST",
			headers: {
				"Content-type": "application/json",
			},
			body: JSON.stringify(dto),
		});
	}

	async createOrder(orderCreationDTO: OrderCreationDTO) {
		return await request<Order>(`${BASE_API_URL}/orders`, {
			method: "POST",
			headers: {
				"Content-type": "application/json",
			},
			body: JSON.stringify(orderCreationDTO),
		});
	}

	async addItems(orderDto: AddItemsDTO) {
		return await request(`${BASE_API_URL}/orders/${orderDto.orderId}/items`, {
			method: "POST",
			headers: {
				"Content-type": "application/json",
			},
			body: JSON.stringify({
				items: orderDto.items,
			}),
		});
	}
}
