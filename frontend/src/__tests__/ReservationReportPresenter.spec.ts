import { describe, expect, it, vi, beforeEach } from "vitest";
import { ReservationReportPresenter } from "../presenters/ReservationReportPresenter";

const mocks = vi.hoisted(() => {
	const getReportData = vi.fn();
	const onError = vi.fn();
	const onSuccess = vi.fn();

	return {
		getReportData,
		viewMock: {
			onError,
			onSuccess,
		},
		reservationServiceMock: vi.fn(() => ({
			getReportData: getReportData,
		})),
	};
});

vi.mock("../models/Reservation/ReservationService", () => ({
	ReservationService: mocks.reservationServiceMock,
}));

describe("ReservationReportPresenter", () => {
	let presenter: ReservationReportPresenter;

	beforeEach(() => {
		presenter = new ReservationReportPresenter(mocks.viewMock);
		vi.clearAllMocks();
	});

	it("should get report data", async () => {
		presenter.getReportData("2024-12-07", "2024-12-07");

		expect(mocks.getReportData).toBeCalled();
	});

	it("should return an empty object when something goes wrong", async () => {
		mocks.getReportData.mockImplementationOnce(() => {
			throw new Error("Generic error");
		});

		const data = await presenter.getReportData("2024-12-07", "2024-12-07");

		expect(data).toEqual({});
	});
});
