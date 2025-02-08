import Toastify from "toastify-js";
import { beforeEach, describe, expect, it, vi } from "vitest";
import { emptyReservationList } from "../components/emptyReservationList";
import { reservationList } from "../components/reservationList";
import { push } from "../lib/push";
import type { Status } from "../models/Reservation/Reservation";
import { ReservationListViewInHtml } from "../views/ReservationListViewInHtml";

const reservationMock = {
	id: 1,
	client: { name: "client one", id: 1 },
	employee: { id: 1, name: "employee one" },
	startTime: "2024-12-07 10:00:00",
	endTime: "2024-12-07 12:00:00",
	status: "active" as Status,
	table: { id: 1, number: 1 },
};

const mocks = vi.hoisted(() => {
	const listReservationMock = vi.fn();

	return {
		presenterMock: vi.fn(() => ({
			listReservations: listReservationMock,
			formatDateToDateDTO: vi.fn(() => "2024-12-06 14:30:00"),
		})),
		toastifyMock: vi.fn(),
	};
});

vi.mock("toastify-js", { spy: true });

vi.mock("../presenters/ReservationListPresenter", () => ({
	ReservationListPresenter: mocks.presenterMock,
}));

describe("ReservationListInHtml", () => {
	let view: ReservationListViewInHtml;

	beforeEach(() => {
		vi.clearAllMocks();
		document.body.innerHTML = "";

		view = new ReservationListViewInHtml({
			currentPage: "test",
			params: {},
			push: push,
			render: (fragment: DocumentFragment) => {
				document.body.appendChild(fragment);
			},
			searchParams: new URLSearchParams(),
		});
	});

	it("should run the flow to list reservations correctly", async () => {
		view.context.searchParams = new URLSearchParams({ page: "1", size: "2" });
		await view.findAll();

		expect(mocks.presenterMock().listReservations).toHaveBeenCalledWith(1, 2);
	});

	it("should set the reservation list template correctly", () => {
		const renderMock = vi.fn();
		view.context.render = renderMock;

		const resultView = reservationList.render({
			nextPageUrl: null,
			prevPageUrl: null,
			reservations: [reservationMock],
			showPagination: false,
		});

		view.showReservationsList({ count: 1, data: [reservationMock] });

		expect(view.template.innerHTML).toBe(resultView);
		expect(renderMock).toHaveBeenCalledWith(view.template.content);
	});

	it("should set the empty reservations list template correctly", () => {
		const renderMock = vi.fn();
		view.context.render = renderMock;

		const resultView = emptyReservationList.render();

		view.showReservationsEmptyList();

		expect(view.template.innerHTML).toBe(resultView);
		expect(renderMock).toHaveBeenCalledWith(view.template.content);
	});

	it("should show a toast when onSuccess is called", () => {
		view.onSuccess("Success message");

		expect(Toastify).toHaveBeenCalledWith({
			className: "has-background-success",
			text: "Success message",
			position: "right",
			style: {
				background:
					"hsl(var(--bulma-success-h), var(--bulma-success-s), var(--bulma-success-l))",
			},
			duration: 3000,
		});
	});

	it("should show a toast when onCreationError is called", () => {
		view.onError("Error message");

		expect(Toastify).toHaveBeenCalledWith({
			text: "Error message",
			position: "right",
			duration: 3000,
			style: {
				background:
					"hsl(var(--bulma-danger-h), var(--bulma-danger-s), var(--bulma-danger-l))",
			},
		});
	});
});
