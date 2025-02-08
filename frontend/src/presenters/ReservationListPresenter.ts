import { BadRequestException } from "../exceptions/bad-request-exception";
import { ReservationService } from "../models/Reservation/ReservationService";

export class ReservationListPresenter {
	private readonly service: ReservationService;
	private readonly view: ReservationListView;

	constructor(view: ReservationListView) {
		this.view = view;
		this.service = new ReservationService();
	}

	public cancelReservation = async (id: string) => {
		try {
			await this.service.cancelReservation(id);

			this.view.onSuccess("Reserva cancelada com sucesso.");
		} catch (err) {
			this.treatErrorException(err);
		}
	};

	public async listReservations(page: number, size: number) {
		try {
			const response = await this.service.findAll(page, size);

			const { count } = response;

			if (count === 0) {
				this.view.showReservationsEmptyList();

				return;
			}

			this.view.showReservationsList(response);
		} catch (err) {
			this.view.onError("Erro ao listar reservas");
		}
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
