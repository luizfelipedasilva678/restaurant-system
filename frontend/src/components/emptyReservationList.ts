import type { Component } from "./component";

export const emptyReservationList: Component = {
	render: () => String.raw`
    <section class="section p-0 pt-5 pb-5" data-testid="section">
      <h3 class="title is-size-3-desktop is-size-4-mobile has-text-centered" data-testid="title">
        Nenhuma reserva cadastrada no momento.
      </h3>
    </section>
  `,
};
