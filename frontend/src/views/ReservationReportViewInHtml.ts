import { reportForm } from "../components/reportForm";
import { initDateInputs } from "../lib/initDateInput";
import type { Context } from "../lib/router";
import { ReservationReportPresenter } from "../presenters/ReservationReportPresenter";
import { BaseView } from "./BaseView";

export class ReservationReportViewInHtml
	extends BaseView
	implements ReservationReportView
{
	private readonly presenter: ReservationReportPresenter;

	constructor(context: Context) {
		super(context);
		this.presenter = new ReservationReportPresenter(this);
	}

	public report = async () => {
		const { Chart, registerables } = await import("chart.js");

		let chart: null | { destroy: () => void } = null;
		Chart.register(...registerables);

		this.template.innerHTML = reportForm.render();

		const initialDateInput = this.template.content.getElementById(
			"initial-date",
		) as HTMLInputElement;

		const finalDateInput = this.template.content.getElementById(
			"final-date",
		) as HTMLInputElement;

		const form = this.template.content.getElementById(
			"get-report-form",
		) as HTMLFormElement;

		const reportContainer = this.template.content.getElementById(
			"report-container",
		) as HTMLCanvasElement;

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

			const reportData = await this.presenter.getReportData(
				target["initial-date"].value,
				target["final-date"].value,
			);

			if (chart) chart.destroy();

			chart = new Chart(reportContainer, {
				type: "bar",
				data: {
					labels: Object.keys(reportData),
					datasets: [
						{
							label: "Número de reservas",
							data: Object.values(reportData),
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
		});

		initDateInputs(initialDateInput, finalDateInput);

		this.context.render(this.template.content);
	};
}
