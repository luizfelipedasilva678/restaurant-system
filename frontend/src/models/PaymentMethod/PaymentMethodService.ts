import { BASE_API_URL } from "../../constants";
import { request } from "../../lib/request";
import type { PaymentMethod } from "./PaymentMethod";

export class PaymentMethodService {
	async findAll() {
		return request<PaymentMethod[]>(`${BASE_API_URL}/payments-methods`);
	}
}
