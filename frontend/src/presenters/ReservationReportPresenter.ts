import { ReservationService } from "../models/Reservation/ReservationService";

export class ReservationReportPresenter {
	private readonly service: ReservationService;
	private readonly view: ReservationReportView;

	constructor(view: ReservationReportView) {
		this.view = view;
		this.service = new ReservationService();
	}

	public getReportData = async (initialDate: string, finalDate: string) => {
		try {
			return await this.service.getReportData(initialDate, finalDate);
		} catch (err) {
			this.view.onError("Erro ao obter dados");

			return {};
		}
	};
}
