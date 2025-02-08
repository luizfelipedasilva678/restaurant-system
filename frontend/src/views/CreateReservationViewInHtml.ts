import { createReservationForm } from "../components/createReservationForm";
import type { Context } from "../lib/router";
import { getSession } from "../lib/sessionUtils";
import { CreateReservationPresenter } from "../presenters/CreateReservationPresenter";
import { BaseView } from "./BaseView";

export class CreateReservationViewInHtml
	extends BaseView
	implements CreateReservationView
{
	private readonly presenter: CreateReservationPresenter;

	constructor(context: Context) {
		super(context);
		this.presenter = new CreateReservationPresenter(this);
	}

	public create = async () => {
		await this.showCreateReservationForm();
	};

	public async showCreateReservationForm() {
		const data = getSession();

		const loggedUserId = data?.session?.user.id ?? 0;

		const tables = await this.presenter.getTables();
		const { data: employees } = await this.presenter.getEmployees();

		this.template.innerHTML = createReservationForm.render({
			tables,
			employees,
			loggedUserId,
		});

		const inputReservationTime = this.template.content.getElementById(
			"reservation-time",
		) as HTMLInputElement;

		const inputName = this.template.content.getElementById(
			"name",
		) as HTMLInputElement;

		const tableSelect = this.template.content.getElementById(
			"table-id",
		) as HTMLSelectElement;

		const employeeSelect = this.template.content.getElementById(
			"employee-id",
		) as HTMLSelectElement;

		const form = this.template.content.getElementById(
			"create-reservation-form",
		) as HTMLFormElement;

		const phoneInput = this.template.content.getElementById(
			"phone",
		) as HTMLInputElement;

		inputReservationTime.addEventListener("focus", () => {
			employeeSelect.setAttribute("disabled", "true");
			tableSelect.setAttribute("disabled", "true");
		});

		inputReservationTime.addEventListener("blur", async () => {
			const selectedDate = this.presenter.formatDateToDateDTO(
				inputReservationTime.value,
			);

			const occupiedTables = await this.presenter.getTables(selectedDate);

			if (occupiedTables.length) {
				for (const { id } of occupiedTables) {
					document
						.querySelectorAll(`[data-table-id="${id}"]`)
						.forEach((el) => el.setAttribute("disabled", "true"));
				}
			} else {
				document
					.querySelectorAll(`[data-table-id][disabled="true"]`)
					.forEach((el) => el.removeAttribute("disabled"));
			}

			employeeSelect.removeAttribute("disabled");
			tableSelect.removeAttribute("disabled");
		});

		form.addEventListener("submit", (event) => {
			event.preventDefault();

			const clientPhone = phoneInput.value;
			const clientName = inputName.value;
			const startTime = this.presenter.formatDateToDateDTO(
				inputReservationTime.value,
			);
			const tableId = Number(tableSelect.value);
			const employeeId = Number(employeeSelect.value);

			this.presenter.createReservation({
				clientName,
				employeeId,
				startTime,
				tableId,
				clientPhone,
			});
		});

		this.context.render(this.template.content);
	}
}
