import { beforeEach, describe, expect, it, vi } from "vitest";
import { ReservationListPresenter } from "../presenters/ReservationListPresenter";

const mocks = vi.hoisted(() => {
	const findAllMock = vi.fn();
	const cancelMock = vi.fn();
	const onError = vi.fn();
	const onSuccess = vi.fn();
	const showReservationsListMock = vi.fn();
	const showReservationsEmptyListMock = vi.fn();

	return {
		findAllMock,
		cancelMock,
		showReservationsListMock,
		showReservationsEmptyListMock,
		viewMock: {
			onError,
			onSuccess,
			showReservationsList: showReservationsListMock,
			showReservationsEmptyList: showReservationsEmptyListMock,
		},
		reservationServiceMock: vi.fn(() => ({
			findAll: findAllMock,
			cancelReservation: cancelMock,
		})),
	};
});

vi.mock("../models/Reservation/ReservationService", () => ({
	ReservationService: mocks.reservationServiceMock,
}));

describe("ReservationListPresenter", () => {
	let presenter: ReservationListPresenter;

	beforeEach(() => {
		vi.clearAllMocks();

		presenter = new ReservationListPresenter(
			mocks.viewMock as unknown as ReservationListView,
		);
	});

	it("should run the flow to list reservations correctly", async () => {
		mocks.findAllMock.mockReturnValueOnce({ count: 10, data: [] });

		await presenter.listReservations(1, 10);

		expect(mocks.findAllMock).toHaveBeenCalledWith(1, 10);
		expect(mocks.showReservationsListMock).toHaveBeenCalledWith({
			count: 10,
			data: [],
		});
	});

	it("should run the flow to show reservations empty list correctly", async () => {
		mocks.findAllMock.mockReturnValueOnce({ count: 0, data: [] });

		await presenter.listReservations(1, 10);

		expect(mocks.findAllMock).toHaveBeenCalledWith(1, 10);
		expect(mocks.showReservationsEmptyListMock).toHaveBeenCalled();
	});

	it("should run the cancel flow correctly", async () => {
		await presenter.cancelReservation("1");

		expect(mocks.cancelMock).toHaveBeenCalledWith("1");
		expect(mocks.viewMock.onSuccess).toHaveBeenCalledWith(
			"Reserva cancelada com sucesso.",
		);
	});

	it("should run the cancel flow correctly on error", async () => {
		mocks.cancelMock.mockImplementationOnce(() => {
			throw new Error();
		});

		await presenter.cancelReservation("1");

		expect(mocks.cancelMock).toHaveBeenCalledWith("1");
		expect(mocks.viewMock.onError).toHaveBeenCalledWith(
			"Erro ao executar tarefa",
		);
	});
});
