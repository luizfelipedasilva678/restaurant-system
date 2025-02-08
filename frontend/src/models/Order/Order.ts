import type { Client } from "../Reservation/Reservation";
import type { Table } from "../Table/Table";

export type OrderStatus = "open" | "completed";

export interface Order {
	id: number;
	status: OrderStatus;
	table: Table;
	client: Client;
	items: OrderItem[];
}

export interface AddItemsDTO {
	orderId: number;
	items: { itemId: number; quantity: number }[];
}

export interface OrderCreationDTO {
	clientName: string;
	tableId: number;
}

export interface OrderItem {
	id: number;
	itemId: number;
	quantity: number;
	price: number;
	description: string;
	category: string;
}

export interface FulFillOrderDTO {
	paymentMethodId: number;
	employeeId: number;
	orderId: number;
	total: number;
	discount: number;
}
