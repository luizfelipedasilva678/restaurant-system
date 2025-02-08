import { each } from "../lib/each";
import { renderIf } from "../lib/renderIf";
import type { Employee } from "../models/Employee/Employee";
import type { Table } from "../models/Table/Table";
import type { Component } from "./component";

interface Props {
	tables: Table[];
	employees: Employee[];
	loggedUserId: number;
}

export const createReservationForm: Component<Props> = {
	render: ({ tables, employees, loggedUserId }) => {
		return String.raw`
      <form class="is-flex is-flex-direction-column mt-6" id="create-reservation-form" data-testid="create-reservation-form">
        <div class="field">
          <label class="label" for="name">Nome do cliente</label>
          <div class="control">
            <input class="input" type="text" placeholder="Digite o nome do cliente" name="name" id="name" required="true" data-testid="field-name">
          </div>
        </div>

        <div class="field">
          <label class="label" for="phone">Telefone do cliente</label>
          <div class="control">
            <input class="input" type="text" placeholder="(22) 22222-2222" pattern="\(\d{2}\)\s(\d{4}|\d{5})-(\d{4})" name="phone" id="phone" required="true" data-testid="field-phone">
          </div>
        </div>

        <div class="field">
          <label class="label" for="reservation-time">Horário da reserva</label>
          <div class="control">
            <input class="input" type="datetime-local" name="reservation-time" id="reservation-time" required="true" data-testid="field-reservation">
          </div>
        </div>

        <div class="field">
          <label class="label" for="table-id">Mesa</label>
          <div class="control">
            <div class="select">
              <select id="table-id" name="table-id" required="true" data-testid="table-select" disabled="true">
                <option required="true">Mesa</option>

                ${each(
									tables,
									({ id, number }) => `
                  <option value="${id}" data-table-id="${id}">Mesa ${number}</option>
                `,
								)}
              </select>
            </div>
          </div>
        </div>

        <div class="field">
          <label class="label" for="employee-id">Funcionário responsável</label>
          <div class="control">
            <div class="select">
              <select id="employee-id" name="employee-id" required="true" data-testid="employee-select" disabled="true">
                <option required="true">Funcionário</option>

                ${each(
									employees,
									({ id, name }) => `
                  <option value="${id}" ${renderIf(loggedUserId === id, 'selected="true"')}>${name}</option>
                `,
								)}
              </select>
            </div>
          </div>
        </div>

        <div class="field is-grouped">
          <div class="control">
            <button class="button is-link" id="create-button" type="submit" data-testid="create-reservation">Criar reservar</button>
            <button class="button is-link is-light" type="reset" data-testid="cancel-reservation">Cancelar</button>
          </div>
        </div>
      </form>
    `;
	},
};
