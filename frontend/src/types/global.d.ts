import type { Menu } from "../lib/menu";
import type { Order } from "../models/Order/Order";
import type { PaymentMethod } from "../models/PaymentMethod/PaymentMethod";
import type {
	SalesByCategoryReport,
	SalesByDayReport,
	SalesByEmployeeReport,
	SalesByPaymentMethodReport,
} from "../models/Reports/Reports";
import type { GetReservationsResponse } from "../models/Reservation/ReservationService";
import type { Table } from "../models/Table/Table";

declare global {
	interface Window {
		Menu: Menu | undefined;
	}

	interface View {
		onSuccess: (message: string, duration = 3000) => void;
		onError: (message: string, duration = 3000) => void;
	}

	interface ReservationListView extends View {
		showReservationsEmptyList: () => void;
		showReservationsList: (data: GetReservationsResponse) => void;
	}

	interface CreateReservationView extends View {}

	interface ReservationReportView extends View {}

	interface AuthView extends View {}

	interface ReportsView extends View {
		showSalesByCategory: (report: SalesByCategoryReport[]) => void;
		showSalesByDay: (report: SalesByDayReport[]) => void;
		showSalesByEmployee: (report: SalesByEmployeeReport[]) => void;
		showSalesByPaymentMethod: (report: SalesByPaymentMethodReport[]) => void;
	}

	interface TablesBeingAttendView extends View {
		showTablesBeingAttended: (
			reservations: Order[],
			items: Item[],
			tables: Table[],
			paymentsMethods: PaymentMethod[],
		) => void;
	}
}
