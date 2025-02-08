import { getByTestId, queryByTestId } from "@testing-library/dom";
import { afterEach, beforeEach, describe, expect, it, vi } from "vitest";
import { reservationList } from "../components/reservationList";
import type { Status } from "../models/Reservation/Reservation";

describe("reservationList", () => {
	const mockProps = {
		nextPageUrl: new URL("/next", "http://localhost"),
		prevPageUrl: new URL("/prev", "http://localhost"),
		showPagination: true,
		reservations: [
			{
				client: { name: "client one", id: 1 },
				employee: { id: 1, name: "employee one" },
				startTime: "2024-12-07 10:00:00",
				endTime: "2024-12-07 12:00:00",
				id: 1,
				status: "active" as Status,
				table: { id: 1, number: 1 },
			},
			{
				client: { name: "client two", id: 2 },
				employee: { id: 2, name: "employee two" },
				startTime: "2024-12-07 14:00:00",
				endTime: "2024-12-07 16:00:00",
				id: 2,
				status: "inactive" as Status,
				table: { id: 2, number: 2 },
			},
		],
	};

	beforeEach(() => {
		vi.useFakeTimers();
	});

	afterEach(() => {
		vi.useRealTimers();
	});

	it("should render the component correctly", () => {
		document.body.innerHTML = reservationList.render(mockProps);

		expect(getByTestId(document.body, "title")).toHaveTextContent(
			"Listagem das reservas",
		);
		expect(getByTestId(document.body, "list")).toBeInTheDocument();
		expect(getByTestId(document.body, "pagination")).toBeInTheDocument();
	});

	it("should render the cancel button for active reservations", () => {
		vi.setSystemTime(new Date(2024, 11, 7, 9, 0, 0));

		document.body.innerHTML = reservationList.render(mockProps);

		expect(getByTestId(document.body, "cancel-trigger-1")).toBeInTheDocument();
	});

	it("should not render the cancel button for inactive reservations", () => {
		vi.setSystemTime(new Date(2024, 11, 7, 9, 0, 0));

		document.body.innerHTML = reservationList.render(mockProps);

		expect(
			queryByTestId(document.body, "cancel-trigger-2"),
		).not.toBeInTheDocument();
	});

	it("should not render the cancel button for finished reservations", () => {
		vi.setSystemTime(new Date(2024, 11, 7, 14, 0, 0));

		document.body.innerHTML = reservationList.render(mockProps);

		expect(
			queryByTestId(document.body, "cancel-trigger-1"),
		).not.toBeInTheDocument();
	});

	it("should render the correct number of cards", () => {
		vi.setSystemTime(new Date(2024, 11, 7, 9, 0, 0));

		document.body.innerHTML = reservationList.render(mockProps);

		expect(getByTestId(document.body, "list").children).toHaveLength(2);
	});

	it("should render the 'Ativa' flag when the reservation is on time", () => {
		vi.setSystemTime(new Date(2024, 11, 7, 9, 0, 0));

		document.body.innerHTML = reservationList.render(mockProps);

		expect(getByTestId(document.body, "status-1")).toHaveTextContent("Ativa");
	});

	it("should render the 'Finalizada' flag when the reservation is finished", () => {
		vi.setSystemTime(new Date(2024, 11, 7, 13, 0, 0));

		document.body.innerHTML = reservationList.render(mockProps);

		expect(getByTestId(document.body, "status-1")).toHaveTextContent(
			"Finalizada",
		);
	});

	it("should render the 'Cancelada' flag when the reservation is canceled", () => {
		vi.setSystemTime(new Date(2024, 11, 7, 13, 0, 0));

		document.body.innerHTML = reservationList.render(mockProps);

		expect(getByTestId(document.body, "status-2")).toHaveTextContent(
			"Cancelada",
		);
	});

	it("should not show the pagination when the flag is false", () => {
		document.body.innerHTML = reservationList.render({
			...mockProps,
			showPagination: false,
		});

		expect(queryByTestId(document.body, "pagination")).not.toBeInTheDocument();
	});

	it("should disable the next link when the next page url is undefined", () => {
		document.body.innerHTML = reservationList.render({
			...mockProps,
			nextPageUrl: null,
		});

		expect(getByTestId(document.body, "pagination-next-url")).toHaveAttribute(
			"disabled",
		);
	});

	it("should disable the prev link when the prev page url is undefined", () => {
		document.body.innerHTML = reservationList.render({
			...mockProps,
			prevPageUrl: null,
		});

		expect(getByTestId(document.body, "pagination-prev-url")).toHaveAttribute(
			"disabled",
		);
	});

	it("should render the reservation info correctly", () => {
		vi.setSystemTime(new Date(2024, 11, 7, 9, 0, 0));

		document.body.innerHTML = reservationList.render(mockProps);

		expect(getByTestId(document.body, "card-1")).toBeInTheDocument();
		expect(getByTestId(document.body, "status-1")).toBeInTheDocument();
		expect(getByTestId(document.body, "client-name-1")).toBeInTheDocument();
		expect(getByTestId(document.body, "employee-name-1")).toBeInTheDocument();
		expect(getByTestId(document.body, "table-number-1")).toBeInTheDocument();
		expect(getByTestId(document.body, "start-date-1")).toBeInTheDocument();
		expect(getByTestId(document.body, "end-date-1")).toBeInTheDocument();
		expect(getByTestId(document.body, "cancel-modal-1")).toBeInTheDocument();
		expect(getByTestId(document.body, "close-modal-1")).toBeInTheDocument();
		expect(
			getByTestId(document.body, "abort-cancel-trigger-1"),
		).toBeInTheDocument();
		expect(
			getByTestId(document.body, "cancel-reservation-trigger-1"),
		).toBeInTheDocument();
		expect(
			getByTestId(document.body, "cancel-modal-background-1"),
		).toBeInTheDocument();
		expect(
			getByTestId(document.body, "cancel-modal-content-1"),
		).toBeInTheDocument();
	});
});
