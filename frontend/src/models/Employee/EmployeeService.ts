import { BASE_API_URL } from "../../constants";
import { request } from "../../lib/request";
import type { Employee } from "./Employee";

interface GetEmployeesResponse {
	data: Employee[];
	count: number;
}

export class EmployeeService {
	async findAll(page = 1, size = 10) {
		return request<GetEmployeesResponse>(
			`${BASE_API_URL}/employees?page=${page}&perPage=${size}`,
		);
	}
}
