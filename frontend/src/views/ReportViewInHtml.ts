import { reportForm } from "../components/reportForm";
import { initDateInputs } from "../lib/initDateInput";
import type { Context } from "../lib/router";
import { ReportsPresenter } from "../presenters/ReportsPresenter";
import { BaseView } from "./BaseView";
import { Chart, registerables } from "chart.js";
import DataLabels from "chartjs-plugin-datalabels";
import type {
	SalesByCategoryReport,
	SalesByDayReport,
	SalesByEmployeeReport,
	SalesByPaymentMethodReport,
} from "../models/Reports/Reports";

export class ReportViewInHtml extends BaseView implements ReportsView {
	private readonly presenter: ReportsPresenter;
	private readonly reportType: ReportType;
	private reportContainer: HTMLCanvasElement;
	private chart: null | { destroy: () => void } = null;

	constructor(context: Context, reportType: ReportType, title: string) {
		super(context);
		this.template.innerHTML = reportForm.render({
			title: title,
		});
		Chart.register(...registerables, DataLabels);
		this.presenter = new ReportsPresenter(this);
		this.reportType = reportType;
	}

	public showSalesByCategory = (report: SalesByCategoryReport[]) => {
		this.initPieChart(
			report.map((category) => category.category),
			report.map((category) => category.sales),
		);
	};

	public showSalesByDay = (report: SalesByDayReport[]) => {
		this.initBarChart(
			report.map((day) => day.date),
			report.map((day) => day.sales),
			"Vendas",
		);
	};

	public showSalesByEmployee = (report: SalesByEmployeeReport[]) => {
		this.initPieChart(
			report.map((employee) => employee.employee),
			report.map((employee) => employee.sales),
		);
	};

	public showSalesByPaymentMethod = (report: SalesByPaymentMethodReport[]) => {
		this.initPieChart(
			report.map((method) => method.payment_method),
			report.map((method) => method.sales),
		);
	};

	private initPieChart(labels: string[], data: number[]) {
		if (this.chart) this.chart.destroy();

		this.chart = new Chart(this.reportContainer, {
			type: "pie",
			data: {
				labels: labels,
				datasets: [
					{
						data: data,
						borderWidth: 1,
					},
				],
			},
			options: {
				plugins: {
					datalabels: {
						formatter: (value, ctx) => {
							const datapoints = ctx.chart.data.datasets[0].data as number[];
							const total = datapoints.reduce((a, b) => a + b, 0);
							const percentage = (value / total) * 100;

							return `${value} (${percentage.toFixed(2)}%)`;
						},
						color: "#fff",
					},
				},
			},
		});
	}

	private initBarChart(labels: string[], data: number[], label: string) {
		if (this.chart) this.chart.destroy();

		this.chart = new Chart(this.reportContainer, {
			type: "bar",
			data: {
				labels: labels,
				datasets: [
					{
						label: label,
						data: data,
						borderWidth: 1,
					},
				],
			},
			options: {
				scales: {
					y: {
						beginAtZero: true,
					},
				},
			},
		});
	}

	public draw = async () => {
		this.reportContainer = this.template.content.getElementById(
			"report-container",
		) as HTMLCanvasElement;

		const initialDateInput = this.template.content.getElementById(
			"initial-date",
		) as HTMLInputElement;

		const finalDateInput = this.template.content.getElementById(
			"final-date",
		) as HTMLInputElement;

		const form = this.template.content.getElementById(
			"get-report-form",
		) as HTMLFormElement;

		form.addEventListener("submit", async (event) => {
			event.preventDefault();

			const target = event.target as HTMLFormElement;
			initialDateInput.classList.remove("is-danger");

			if (
				target.reportValidity() === false ||
				new Date(target["initial-date"].value) >
					new Date(target["final-date"].value)
			) {
				initialDateInput.classList.add("is-danger");

				this.onError(
					"Os campos devem ser preenchidos corretamente. A data inicial deve ser menor que a data final.",
					6000,
				);

				return;
			}

			const initialDate = target["initial-date"].value;
			const finalDate = target["final-date"].value;

			switch (this.reportType) {
				case "sales-by-category":
					await this.presenter.getReportByCategory(initialDate, finalDate);
					break;
				case "sales-by-day":
					await this.presenter.getReportByDay(initialDate, finalDate);
					break;
				case "sales-by-employee":
					await this.presenter.getReportByEmployee(initialDate, finalDate);
					break;
				case "sales-by-payment-method":
					await this.presenter.getReportByPaymentMethod(initialDate, finalDate);
					break;
			}
		});

		initDateInputs(initialDateInput, finalDateInput);

		this.context.render(this.template.content);
	};
}

type ReportType =
	| "sales-by-category"
	| "sales-by-day"
	| "sales-by-employee"
	| "sales-by-payment-method";
