import type { Employee } from "../Employee/Employee";
import type { Table } from "../Table/Table";

export type Status = "active" | "inactive";

export interface Client {
	id: number;
	name: string;
	phone: string;
}

export interface Reservation {
	id: number;
	status: Status;
	startTime: string;
	endTime: string;
	table: Table;
	employee: Employee;
	client: Client;
}

export interface ReservationDTO {
	clientName: string;
	clientPhone: string;
	tableId: number;
	employeeId: number;
	startTime: string;
}
