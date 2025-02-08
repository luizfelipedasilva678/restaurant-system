import { getByTestId } from "@testing-library/dom";
import { beforeEach, describe, expect, it } from "vitest";
import { emptyReservationList } from "../components/emptyReservationList";

describe("emptyReservationList", () => {
	beforeEach(() => {
		document.body.innerHTML = emptyReservationList.render();
	});

	it("should render the component correctly", () => {
		expect(getByTestId(document.body, "section")).toBeInTheDocument();
		expect(getByTestId(document.body, "title")).toBeInTheDocument();
		expect(getByTestId(document.body, "title")).toHaveTextContent(
			"Nenhuma reserva cadastrada no momento.",
		);
	});
});
