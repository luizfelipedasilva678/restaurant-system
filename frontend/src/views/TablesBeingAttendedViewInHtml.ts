import { tablesBeingAttended } from "../components/tablesBeingAttended";
import { formatPrice } from "../lib/formatPrice";
import type { Context } from "../lib/router";
import type { Item } from "../models/Item/Item";
import type { AddItemsDTO, Order } from "../models/Order/Order";
import type { PaymentMethod } from "../models/PaymentMethod/PaymentMethod";
import type { Table } from "../models/Table/Table";
import { TablesBeingAttendedListPresenter } from "../presenters/TablesBeingAttendedListPresenter";
import { BaseView } from "./BaseView";

export class TablesBeingAttendedViewInHtml
	extends BaseView
	implements TablesBeingAttendView
{
	private readonly presenter: TablesBeingAttendedListPresenter;

	constructor(context: Context) {
		super(context);
		this.presenter = new TablesBeingAttendedListPresenter(this);
	}

	public draw = async () => {
		await this.presenter.getTablesBeingAttended();
	};

	private dispatchInputsChangedEvent(
		form: HTMLFormElement,
		formInputs: HTMLInputElement[],
	) {
		const inputsChangedEvt = new CustomEvent("inputs-changed", {
			detail: {
				totalQuantity: formInputs.reduce((acc, input) => acc + +input.value, 0),
			},
		});

		form.dispatchEvent(inputsChangedEvt);
	}

	private attachEventListenersToQuantityButtons(
		consumptionsForm: HTMLFormElement,
	) {
		const formInputs = Array.from(
			consumptionsForm.querySelectorAll<HTMLInputElement>("input"),
		);

		for (const input of formInputs) {
			const inputId = input.id;
			const addButton = consumptionsForm.querySelector<HTMLButtonElement>(
				`[data-add-quantity-for='${inputId}']`,
			);
			const removeButton = consumptionsForm.querySelector<HTMLButtonElement>(
				`[data-remove-quantity-for='${inputId}']`,
			);

			if (addButton && removeButton) {
				addButton.addEventListener("click", () => {
					input.value = String(+input.value + 1);
					this.dispatchInputsChangedEvent(consumptionsForm, formInputs);
				});

				removeButton.addEventListener("click", () => {
					if (+input.value === 0) return;
					input.value = String(+input.value - 1);
					this.dispatchInputsChangedEvent(consumptionsForm, formInputs);
				});
			}
		}
	}

	private readonly fulfillOrder = () => {
		const fulfillOrderModal = this.template.content.querySelector(
			"#order-fulfill-modal",
		) as HTMLDivElement;
		const fulfillOrderForm = this.template.content.querySelector(
			"#order-fulfill-form",
		) as HTMLDivElement;
		const fulfillOrderCloseModalBtn = fulfillOrderModal.querySelector(
			".modal-close",
		) as HTMLDivElement;
		const fulfillOrderButtons =
			this.template.content.querySelectorAll<HTMLButtonElement>(
				"[fulfill-order]",
			);
		const paymentMethodSelect = this.template.content.querySelector(
			"#payment-method-id",
		) as HTMLSelectElement;
		const discountInput = this.template.content.querySelector(
			"#discount-percentage",
		) as HTMLInputElement;
		const orderTotalDisplay = this.template.content.querySelector(
			"#order-total",
		) as HTMLParagraphElement;
		const orderTotalWithDiscountDisplay = this.template.content.querySelector(
			"#order-total-with-discount",
		) as HTMLParagraphElement;

		for (const button of fulfillOrderButtons) {
			button.addEventListener("click", () => {
				fulfillOrderModal.classList.add("is-active");

				const orderId = button.dataset.orderId;
				const clientId = button.dataset.clientId;
				const tableId = button.dataset.tableId;
				const total = button.dataset.total;

				orderId && fulfillOrderForm.setAttribute("data-order-id", orderId);
				clientId && fulfillOrderForm.setAttribute("data-client-id", clientId);
				tableId && fulfillOrderForm.setAttribute("data-table-id", tableId);
				total && fulfillOrderForm.setAttribute("data-total", total);

				orderTotalDisplay.textContent = `Valor do atendimento: ${formatPrice(
					Number(total),
				)}`;
			});
		}

		discountInput.addEventListener("change", () => {
			const total = Number(fulfillOrderForm.dataset.total);
			const discount = Number(discountInput.value);

			const valueWithDiscount =
				!Number.isNaN(discount) && discount > 0
					? total - total * (discount / 100)
					: total;

			orderTotalWithDiscountDisplay.textContent = `Valor do atendimento com desconto: ${formatPrice(
				Number(valueWithDiscount),
			)}`;
		});

		fulfillOrderCloseModalBtn.addEventListener("click", () => {
			fulfillOrderModal.classList.remove("is-active");
		});

		fulfillOrderForm.addEventListener("submit", (event) => {
			event.preventDefault();

			const orderId = fulfillOrderForm.dataset.orderId ?? "";
			const total = fulfillOrderForm.dataset.total ?? "";
			const paymentMethodId = paymentMethodSelect?.value ?? "";
			const discount = discountInput?.value ?? "";

			this.presenter.fulfill({
				orderId,
				paymentMethodId,
				total,
				discount,
			});
		});
	};

	public showTablesBeingAttended = async (
		orders: Order[],
		items: Item[],
		tables: Table[],
		paymentsMethods: PaymentMethod[],
	) => {
		try {
			this.template.innerHTML = tablesBeingAttended.render({
				orders,
				items,
				tables,
				paymentsMethods,
			});

			this.fulfillOrder();

			const section = this.template.content.querySelector(
				"#tables-being-attended",
			);
			const consumptionsModal =
				this.template.content.querySelector<HTMLElement>("#consumptions-modal");
			const consumptionsModalCloseButton =
				this.template.content.querySelector<HTMLElement>(
					"#consumptions-modal .modal-close",
				);
			const consumptionsForm =
				this.template.content.querySelector<HTMLFormElement>(
					"#consumptions-form",
				);
			const consumptionsFormSubmitButton =
				this.template.content.querySelector<HTMLButtonElement>(
					"#consumptions-form button[type='submit']",
				);
			const orderCreationForm =
				this.template.content.querySelector<HTMLFormElement>(
					"#order-creation-form",
				);
			const orderCreationModal =
				this.template.content.querySelector<HTMLFormElement>(
					"#order-creation-modal",
				);
			const orderCreationCloseModalBtn =
				this.template.content.querySelector<HTMLButtonElement>(
					"#order-creation-modal .modal-close",
				);

			if (orderCreationForm) {
				orderCreationForm.addEventListener("submit", async (e) => {
					e.preventDefault();

					const form = e.target as HTMLFormElement;

					if (form.reportValidity() === false) {
						this.onError("Preencha todos os campos corretamente.");
						return;
					}

					const tableIdSelect = form["table-id"] as HTMLSelectElement;
					const clientNameInput = form["client-name"] as HTMLInputElement;

					await this.presenter.createOrder({
						clientName: clientNameInput.value,
						tableId: Number(tableIdSelect.value),
					});
				});
			}

			if (consumptionsForm) {
				this.attachEventListenersToQuantityButtons(consumptionsForm);

				consumptionsForm.addEventListener("inputs-changed", ((
					event: CustomEvent,
				) => {
					event.detail.totalQuantity > 0
						? consumptionsFormSubmitButton?.removeAttribute("disabled")
						: consumptionsFormSubmitButton?.setAttribute("disabled", "true");
				}) as EventListener);

				consumptionsForm.addEventListener("submit", async (e) => {
					e.preventDefault();

					const form = e.target as HTMLFormElement;

					const orderId = Number(form.dataset.orderId ?? "0");
					const inputs = form.querySelectorAll<HTMLInputElement>("input");
					const addItemsDto: AddItemsDTO = {
						orderId,
						items: [],
					};

					for (const input of inputs) {
						const value = +input.value;
						const itemId = Number(input.dataset.itemId ?? 0);

						if (value <= 0) continue;

						addItemsDto.items.push({
							itemId: itemId,
							quantity: value,
						});
					}

					await this.presenter.addConsumptions(addItemsDto);
				});
			}

			orderCreationCloseModalBtn?.addEventListener("click", () => {
				orderCreationModal?.classList.remove("is-active");
			});

			consumptionsModalCloseButton?.addEventListener("click", () => {
				consumptionsModal?.classList.remove("is-active");
			});

			if (section) {
				section.addEventListener("click", (e) => {
					const target = e.target as HTMLElement;

					if (target.id === "create-order-button") {
						orderCreationModal?.classList.add("is-active");
					} else if (target.getAttribute("launch-consumptions")) {
						const orderId = target.dataset.orderId ?? "0";

						consumptionsForm?.setAttribute("data-order-id", orderId);
						consumptionsModal?.classList.add("is-active");
					}
				});
			}

			this.context.render(this.template.content);
		} catch (err) {
			this.onError("Ocorreu um erro ao carregar a página");
		}
	};

	public onSuccess(message: string, duration?: number): void {
		super.onSuccess(message, duration, () =>
			this.context.push("/tables-being-attended"),
		);
	}
}
