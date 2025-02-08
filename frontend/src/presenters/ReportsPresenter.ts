import { ReportsService } from "../models/Reports/ReportService";

export class ReportsPresenter {
	private readonly reportService: ReportsService;
	private readonly view: ReportsView;

	public constructor(view: ReportsView) {
		this.reportService = new ReportsService();
		this.view = view;
	}

	public getReportByCategory = async (initialDate: string, endDate: string) => {
		const report = await this.reportService.getReportByCategory(
			initialDate,
			endDate,
		);

		this.view.showSalesByCategory(report);
	};

	public getReportByDay = async (initialDate: string, endDate: string) => {
		const report = await this.reportService.getReportByDay(
			initialDate,
			endDate,
		);

		this.view.showSalesByDay(report);
	};

	public getReportByEmployee = async (initialDate: string, endDate: string) => {
		const report = await this.reportService.getReportByEmployee(
			initialDate,
			endDate,
		);

		this.view.showSalesByEmployee(report);
	};

	public getReportByPaymentMethod = async (
		initialDate: string,
		endDate: string,
	) => {
		const report = await this.reportService.getReportByPaymentMethod(
			initialDate,
			endDate,
		);

		this.view.showSalesByPaymentMethod(report);
	};
}
