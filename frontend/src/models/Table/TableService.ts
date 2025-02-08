import { BASE_API_URL } from "../../constants";
import { request } from "../../lib/request";
import type { Table } from "./Table";

export class TableService {
	async findAll(startDate = "") {
		const url = new URL(`${BASE_API_URL}/tables`);

		if (startDate) {
			url.searchParams.set("startDate", startDate);
		}

		return request<Table[]>(url.toString());
	}
}
