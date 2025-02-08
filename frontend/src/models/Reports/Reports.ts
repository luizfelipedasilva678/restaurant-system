export interface SalesByDayReport {
	date: string;
	sales: number;
}

export interface SalesByCategoryReport {
	category: string;
	sales: number;
}

export interface SalesByPaymentMethodReport {
	payment_method: string;
	sales: number;
}

export interface SalesByEmployeeReport {
	employee: string;
	sales: number;
}
