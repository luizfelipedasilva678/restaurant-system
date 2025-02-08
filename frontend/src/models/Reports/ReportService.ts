import { BASE_API_URL } from "../../constants";
import { request } from "../../lib/request";
import type {
	SalesByCategoryReport,
	SalesByDayReport,
	SalesByEmployeeReport,
	SalesByPaymentMethodReport,
} from "./Reports";

export class ReportsService {
	async getReportByCategory(initialDate: string, endDate: string) {
		try {
			return await request<SalesByCategoryReport[]>(
				`${BASE_API_URL}/reports/sales-by-category?initialDate=${initialDate}&finalDate=${endDate}`,
			);
		} catch (_) {
			return [];
		}
	}

	async getReportByDay(initialDate: string, endDate: string) {
		try {
			return await request<SalesByDayReport[]>(
				`${BASE_API_URL}/reports/sales-by-day?initialDate=${initialDate}&finalDate=${endDate}`,
			);
		} catch (_) {
			return [];
		}
	}

	async getReportByEmployee(initialDate: string, endDate: string) {
		try {
			return await request<SalesByEmployeeReport[]>(
				`${BASE_API_URL}/reports/sales-by-employee?initialDate=${initialDate}&finalDate=${endDate}`,
			);
		} catch (_) {
			return [];
		}
	}

	async getReportByPaymentMethod(initialDate: string, endDate: string) {
		try {
			return await request<SalesByPaymentMethodReport[]>(
				`${BASE_API_URL}/reports/sales-by-payment-method?initialDate=${initialDate}&finalDate=${endDate}`,
			);
		} catch (_) {
			return [];
		}
	}
}
