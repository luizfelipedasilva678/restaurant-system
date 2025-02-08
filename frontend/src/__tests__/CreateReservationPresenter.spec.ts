import { beforeEach, describe, expect, it, vi } from "vitest";
import { CreateReservationPresenter } from "../presenters/CreateReservationPresenter";

const mocks = vi.hoisted(() => {
	const findAllMock = vi.fn();
	const createMock = vi.fn();
	const onError = vi.fn();
	const onSuccess = vi.fn();

	return {
		createMock,
		findAllMock,
		viewMock: {
			onError,
			onSuccess,
		},
		employeeServiceMock: vi.fn(() => ({
			findAll: findAllMock,
		})),
		tableServiceMock: vi.fn(() => ({
			findAll: findAllMock,
		})),
		reservationServiceMock: vi.fn(() => ({
			findAll: findAllMock,
			create: createMock,
		})),
	};
});

vi.mock("../models/Table/TableService", () => ({
	TableService: mocks.tableServiceMock,
}));

vi.mock("../models/Employee/EmployeeService", () => ({
	EmployeeService: mocks.employeeServiceMock,
}));

vi.mock("../models/Reservation/ReservationService", () => ({
	ReservationService: mocks.reservationServiceMock,
}));

describe("CreateReservationPresenter", () => {
	let presenter: CreateReservationPresenter;

	beforeEach(() => {
		vi.clearAllMocks();

		presenter = new CreateReservationPresenter(
			mocks.viewMock as unknown as CreateReservationView,
		);
	});

	it("should format some date to dto format correctly", () => {
		expect(presenter.formatDateToDateDTO("2024-12-06T14:30")).toBe(
			"2024-12-06 14:30:00",
		);
		expect(presenter.formatDateToDateDTO("2024-11-05T16:00")).toBe(
			"2024-11-05 16:00:00",
		);
		expect(presenter.formatDateToDateDTO("2024-10-05T11:00")).toBe(
			"2024-10-05 11:00:00",
		);
	});

	it("should getTables correctly", async () => {
		mocks.findAllMock.mockReturnValueOnce([]);

		await presenter.getTables();

		expect(mocks.findAllMock).toHaveBeenCalled();
	});

	it("should getEmployees correctly", async () => {
		mocks.findAllMock.mockReturnValueOnce([]);

		await presenter.getEmployees();

		expect(mocks.findAllMock).toHaveBeenCalled();
	});

	it("should run the creation flow correctly", async () => {
		const dto = {
			clientName: "test",
			employeeId: 1,
			tableId: 1,
			startTime: "2020-12-01 10:00:00",
		};

		await presenter.createReservation(dto);

		expect(mocks.createMock).toHaveBeenCalledWith(dto);
		expect(mocks.viewMock.onSuccess).toHaveBeenCalledWith(
			"Reserva feita com sucesso.",
		);
	});

	it("should run the creation flow correctly on error", async () => {
		const dto = {
			clientName: "test",
			employeeId: 1,
			tableId: 1,
			startTime: "2020-12-01 10:00:00",
		};

		mocks.createMock.mockImplementationOnce(() => {
			throw new Error();
		});

		await presenter.createReservation(dto);

		expect(mocks.createMock).toHaveBeenCalledWith(dto);
		expect(mocks.viewMock.onError).toHaveBeenCalledWith(
			"Erro ao executar tarefa",
		);
	});
});
