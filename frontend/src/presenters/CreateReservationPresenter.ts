import { BadRequestException } from "../exceptions/bad-request-exception";
import { EmployeeService } from "../models/Employee/EmployeeService";
import type { ReservationDTO } from "../models/Reservation/Reservation";
import { ReservationService } from "../models/Reservation/ReservationService";
import { TableService } from "../models/Table/TableService";

export class CreateReservationPresenter {
	private readonly service: ReservationService;
	private readonly view: CreateReservationView;
	private readonly employeeService: EmployeeService;
	private readonly tableService: TableService;

	constructor(view: CreateReservationView) {
		this.employeeService = new EmployeeService();
		this.tableService = new TableService();
		this.service = new ReservationService();
		this.view = view;
	}

	public getTables = async (startDate = "") => {
		try {
			return await this.tableService.findAll(startDate);
		} catch (err: unknown) {
			this.treatErrorException(err);

			return [];
		}
	};

	public getEmployees = async () => {
		try {
			return await this.employeeService.findAll();
		} catch (err: unknown) {
			this.treatErrorException(err);

			return {
				data: [],
				count: 0,
			};
		}
	};

	public createReservation = async (dto: ReservationDTO) => {
		try {
			await this.service.create(dto);

			this.view.onSuccess("Reserva feita com sucesso.");
		} catch (err) {
			this.treatErrorException(err);
		}
	};

	public formatDateToDateDTO(datetime: string) {
		const formatLeft = (num: number) => num.toString().padStart(2, "0");

		const date = new Date(datetime);

		const year = date.getFullYear();
		const month = formatLeft(date.getMonth() + 1);
		const day = formatLeft(date.getDate());

		const hour = formatLeft(date.getHours());
		const minute = formatLeft(date.getMinutes());
		const second = formatLeft(date.getSeconds());

		return `${year}-${month}-${day} ${hour}:${minute}:${second}`;
	}

	private treatErrorException(err: unknown) {
		if (err instanceof BadRequestException) {
			const { errorMessage, errorsMessages } = err;

			if (errorMessage) {
				this.view.onError(errorMessage);

				return;
			}

			for (const msg of errorsMessages) {
				this.view.onError(msg);
			}

			return;
		}

		this.view.onError("Erro ao executar tarefa");
	}
}
