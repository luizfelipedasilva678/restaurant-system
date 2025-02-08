import { BASE_API_URL } from "../../constants";
import { request } from "../../lib/request";
import type { Item } from "./Item";

export interface GetItemsResponse {
	data: Item[];
	count: number;
}

export class ItemService {
	async getAllItems() {
		return request<GetItemsResponse>(`${BASE_API_URL}/items`);
	}
}
