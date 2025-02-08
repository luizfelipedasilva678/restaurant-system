import { renderIf } from "../lib/renderIf";
import type { Component } from "./component";

interface Props {
	title?: string;
}

export const reportForm: Component<Props> = {
	render: (props = {}) => String.raw`
    <section class="section p-0 pt-5 pb-5">
      ${renderIf(
				!!props.title,
				String.raw`
          <h3 class="title is-size-3-desktop is-size-4-mobile" data-testid="title">
            ${props.title}
          </h3>
          `,
			)}
      <form class="is-flex is-flex-direction-column mt-6" id="get-report-form" data-testid="get-report-form">
        <div class="field">
          <label class="label" for="initial-date">Data inicial</label>
          <div class="control">
            <input class="input" type="date" name="initial-date" id="initial-date" required data-testid="initial-date">
          </div>
        </div>
        <div class="field">
          <label class="label" for="final-date">Data final</label>
          <div class="control">
            <input class="input" type="date" name="final-date" id="final-date" required data-testid="final-date">
          </div>
        </div>
        <button class="button mt-6" type="submit" id="get-report-button" data-testid="get-report-button">Gerar relatório</button>
      </form>
      <canvas class="is-flex mt-6" id="report-container">
      </canvas>
    </section>
  `,
};
