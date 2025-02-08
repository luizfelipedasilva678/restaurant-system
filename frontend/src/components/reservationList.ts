import { classnames } from "../lib/classnames";
import { each } from "../lib/each";
import { formatDate } from "../lib/formatDate";
import { renderIf } from "../lib/renderIf";
import type { Reservation } from "../models/Reservation/Reservation";
import type { Component } from "./component";

interface Props {
	reservations: Reservation[];
	showPagination: boolean;
	nextPageUrl: URL | null;
	prevPageUrl: URL | null;
}

const getReservationStatus = (status: string, endTime: string) => {
	if (status === "inactive") return "Cancelada";

	if (new Date(endTime) < new Date()) {
		return "Finalizada";
	}

	return "Ativa";
};

export const reservationList: Component<Props> = {
	render: ({ reservations, showPagination, nextPageUrl, prevPageUrl }) => {
		return String.raw`
      <section class="section p-0 pt-5 pb-5">
        <h3 class="title is-size-3-desktop is-size-4-mobile" data-testid="title">
          Listagem das reservas
        </h3>

        <ul data-testid="list">
          ${each(
						reservations,
						({ client, employee, endTime, startTime, status, table, id }) => {
							const { name: clientName, phone } = client;
							const { name: employeeName } = employee;
							const { number: tableNumber } = table;

							const reservationStatus = getReservationStatus(status, endTime);

							return String.raw`
                <li class="card" data-reservation-id="${id}" data-testid="card-${id}">
                  <div class="card-content" data-testid="card-content">
                    ${renderIf(
											reservationStatus === "Ativa",
											`
                        <button class="button is-danger f-right has-text-white" data-cancel-trigger data-testid=${`cancel-trigger-${id}`}>Cancelar reserva</button>
                      `,
										)}

                    <p class="is-flex mb-5" data-testid="status-${id}">
                      <span class="dot mr-3 ${classnames({
												"has-background-success": reservationStatus === "Ativa",
												"has-background-danger":
													reservationStatus === "Cancelada",
												"has-background-warning":
													reservationStatus === "Finalizada",
											})}"></span>
                      
                      ${reservationStatus}
                    </p>
                    <p data-testid="client-name-${id}"><strong>Cliente:</strong> ${clientName}</p>
                    <p data-testid="client-phone-${id}"><strong>Telegone do Cliente:</strong> ${phone}</p>
                    <p data-testid="employee-name-${id}"><strong>Funcionário responsável:</strong> ${employeeName}</p>
                    <p data-testid="table-number-${id}"><strong>Número da mesa:</strong> ${tableNumber}</p>
                    <p data-testid="start-date-${id}"><strong>Data início:</strong> ${formatDate(
											startTime,
										)}</p>
                    <p data-testid="end-date-${id}"><strong>Data fim:</strong> ${formatDate(
											endTime,
										)}</p>
                  </div>

                  <div class="modal" data-cancel-modal="true" data-testid="cancel-modal-${id}">
                    <div class="modal-background" data-testid="cancel-modal-background-${id}"></div>

                    <div class="modal-content" data-testid="cancel-modal-content-${id}">
                      <h3 class="has-text-centered title has-text-white is-size-5" data-testid="modal-title-${id}">Deseja realmente cancelar a reserva?</h3>

                      <div class="is-flex has-align-center is-justify-content-center is-align-items-center">
                        <button class="button mr-2" id="cancel-reservation-trigger" data-testid="cancel-reservation-trigger-${id}">Sim</button>
                        <button class="button is-light" data-abort-cancel="true" data-testid="abort-cancel-trigger-${id}">Não</button>
                      </div>
                    </div>
                    
                    <button class="modal-close is-large" aria-label="close" data-testid="close-modal-${id}"></button>
                  </div>
                </li>
              `;
						},
					)}
        </ul>
          
        ${renderIf(
					showPagination,
					String.raw`
            <nav class="pagination is-medium mt-5" role="navigation" aria-label="pagination" data-testid="pagination">
                <ul class="is-flex">
                  <li>
                    <a href=${prevPageUrl ?? "#"} 
                    class="${classnames(
											{ "events-none": !prevPageUrl },
											"pagination-previous",
										)}" ${renderIf(
											!prevPageUrl,
											"disabled",
										)} data-router-link data-testid="pagination-prev-url">
                      Anterior
                    </a>
                  </li>
                  <li>
                    <a href=${nextPageUrl ?? "#"} class="${classnames(
											{ "events-none": !nextPageUrl },
											"pagination-next",
										)}" ${renderIf(
											!nextPageUrl,
											"disabled",
										)} data-router-link data-testid="pagination-next-url">
                      Próxima
                    </a>
                  </li>
                </ul>
            </nav>
          `,
				)}
      </section>
    `;
	},
};
