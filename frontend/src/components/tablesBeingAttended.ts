import { each } from "../lib/each";
import priceFormatter from "../lib/priceFormatter";
import { renderIf } from "../lib/renderIf";
import { getSession } from "../lib/sessionUtils";
import type { Item } from "../models/Item/Item";
import type { Order, OrderItem } from "../models/Order/Order";
import type { PaymentMethod } from "../models/PaymentMethod/PaymentMethod";
import type { Table } from "../models/Table/Table";
import type { Component } from "./component";

interface Props {
	orders: Order[];
	items: Item[];
	tables: Table[];
	paymentsMethods: PaymentMethod[];
}

export const tablesBeingAttended: Component<Props> = {
	render: ({ orders, items, tables, paymentsMethods }) => {
		const userType = getSession()?.session?.user.type;

		const availableTables = tables.filter(
			(table) => !orders.find((order) => order.table.id === table.id),
		);

		const itemsByCategory: Record<string, Item[]> = items.reduce(
			(acc, item) => {
				const { category } = item;

				if (category in acc) {
					acc[category].push(item);
				} else {
					acc[category] = [item];
				}

				return acc;
			},
			{} as Record<string, Item[]>,
		);

		return String.raw`
      <section class="section p-0 pt-5 pb-5" id="tables-being-attended">
        <button class="is-link button mb-6" id="create-order-button">
          Adicionar atendimento
        </button>

        <h3 class="title is-size-3-desktop is-size-4-mobile" data-testid="title">
          Mesas sendo atendidas
        </h3>

        ${renderIf(
					orders.length === 0,
					String.raw`
            <h3 class="is-size-4-desktop is-size-4-mobile has-text-centered" data-testid="title">
              Nenhuma mesa sendo atendida no momento.
            </h3>
          `,
				)}
        
         <ul data-testid="tables-being-attended-list">
          ${each(
						orders,
						({
							id,
							client: { name, id: clientId },
							table: { number },
							items,
						}) => {
							const consumedItems = items.reduce(
								(acc, item) => {
									if (item.description in acc) {
										acc[item.description] = {
											...item,
											quantity: acc[item.description].quantity + item.quantity,
										};
									} else {
										acc[item.description] = {
											...item,
										};
									}

									return acc;
								},
								{} as Record<string, OrderItem>,
							);

							const total = Object.values(consumedItems).reduce(
								(acc, item) => acc + item.price * item.quantity,
								0,
							);

							const numberOfConsumedItems = Object.values(consumedItems).length;

							return String.raw`
                <li class="card" data-testid="card-${id}" data-order-id="${id}">
                  <div class="is-flex is-justify-content-flex-start pt-4 pl-4">
                    <button class="button is-link f-right has-text-white" launch-consumptions='true' data-order-id="${id}"  >
                      Lançar consumos
                    </button>

                    ${renderIf(
											userType === "manager",
											`
                      <button class="button is-link f-right has-text-white ml-2" fulfill-order='true' data-order-id="${id}" data-client-id="${clientId}" data-total="${total}" ${renderIf(
												numberOfConsumedItems <= 0,
												"disabled",
											)}>
                        Finalizar atendimento
                      </button>
                    `,
										)}
                  </div>
                  <div class="card-content" data-testid="card-content">
                    <p data-testid="client-name-${id}"><strong>Cliente:</strong> ${name}</p>
                    <p data-testid="table-number-${id}"><strong>Mesa:</strong> ${number}</p>
                    <div data-client-consumptions>
                      ${renderIf(
												items.length === 0,
												String.raw`
                          <p>Nenhum consumo registrado.</p>
                        `,
											)}
                      ${each(Object.keys(consumedItems), (consumedItem) => {
												const { description, quantity, price } =
													consumedItems[consumedItem];

												return String.raw`
                          <p>${description} x ${quantity} - ${priceFormatter(
														price,
													)}</p>
                        `;
											})}
                    </div>
                  </div>
                </li>
              `;
						},
					)}
        </ul>

        <div class="modal " id="order-fulfill-modal">
          <div class="modal-background"></div>
          <div class="modal-content">
            <form class="box" id="order-fulfill-form">
              <div class="field">
                <label for="payment-method-id" class="label">Método de pagamento</label>
                <div class="select">
                  <select id="payment-method-id" required>
                    ${each(
											paymentsMethods,
											({ id, name }) => `
                        <option value="${id}">${name}</option>
                      `,
										)}
                  </select>
                </div>
              </div>
              <div class="field">
                <label for="discount" class="label">Desconto em %</label>
                <input class="input" type="number" id="discount-percentage" required min="0" max="5"/>
              </div>
              <div class="field">
                  <p id="order-total"></p>
                  <p id="order-total-with-discount"></p>
              </div>
              <button class="button is-link" id="fulfill-order">Realizar pagamento</button>
            </form>
          </div>
          <button class="modal-close is-large" aria-label="close"></button>
        </div>

        <div class="modal " id="order-creation-modal">
          <div class="modal-background"></div>
          <div class="modal-content">
            <form class="box" id="order-creation-form">
              <div class="field">
                <label for="client-name" class="label">Nome do cliente</label>
                <div class="control">
                  <input class="input" type="text" placeholder="Nome do cliente" id="client-name" required/>
                </div>
              </div>
              <div class="field">
                <label for="table-id" class="label">Mesa</label>
                <div class="select">
                  <select id="table-id" required>
                    ${each(
											availableTables,
											({ id, number }) => `
                        <option value="${id}">${number}</option>
                      `,
										)}
                  </select>
                </div>
              </div>
              <button class="button is-link" id="create-order">Criar atendimento</button>
            </form>
          </div>
          <button class="modal-close is-large" aria-label="close"></button>
        </div>

        <div class="modal" id="consumptions-modal">
          <div class="modal-background"></div>
          <div class="modal-content p-4" >
            <form class="box" id="consumptions-form">
              ${each(Object.keys(itemsByCategory), (category) => {
								return String.raw`
                  <div >
                    <h3 class="is-size-5">${category}</h3>
                    <div class="is-flex is-flex-direction-column is-flex-wrap-wrap category-items-container mt-4 mb-4">
                    ${each(itemsByCategory[category], (item) => {
											return String.raw`
                          <div class="field">
                            <label for="${
															item.code
														}" class="label is-size-6-desktop is-size-6-mobile">${
															item.description
														} - ${priceFormatter(item.price)}</label>
                            <div class="control is-flex is-flex-wrap-wrap item-container" >
                              <input class="input" value="0" min="0" type="number" id="${
																item.code
															}" data-item-id="${item.id}" data-item-code="${
																item.code
															}"/>
                              <div class="is-flex">
                                <button class="button is-link mr-2" type="button" data-add-quantity-for="${
																	item.code
																}">
                                  +    
                                </button>
                                <button class="button is-link" type="button" data-remove-quantity-for="${
																	item.code
																}">
                                  -    
                                </button>
                              </div>
                            </div>
                          </div>
                      `;
										})}
                    </div> 
                  </div>
                `;
							})}
              <button class="button is-link" type="submit" disabled>Lançar</button>
            </form>
          </div>
          <button class="modal-close is-large" aria-label="close"></button>
        </div>
      </section>
    `;
	},
};
