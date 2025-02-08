import { beforeEach, describe, expect, it, vi } from "vitest";
import { TablesBeingAttendedListPresenter } from "../presenters/TablesBeingAttendedListPresenter";
import { BadRequestException } from "../exceptions/bad-request-exception";

const mocks = vi.hoisted(() => {
	const onError = vi.fn();
	const onSuccess = vi.fn();
	const showTablesBeingAttended = vi.fn();
	const createOrder = vi.fn();
	const addItems = vi.fn();
	const getOrder = vi.fn();
	const getOrders = vi.fn();
	const findAll = vi.fn();
	const getAllItems = vi.fn();
	const getPaymentsMethods = vi.fn();

	return {
		onError,
		onSuccess,
		showTablesBeingAttended,
		createOrder,
		addItems,
		getOrder,
		getOrders,
		findAll,
		getAllItems,
		getPaymentsMethods,
		viewMock: {
			onError,
			onSuccess,
			showTablesBeingAttended,
		},
		tableService: vi.fn(() => ({
			findAll,
		})),
		paymentMethodService: vi.fn(() => ({
			findAll: getPaymentsMethods,
		})),
		orderServiceMock: vi.fn(() => ({
			createOrder,
			addItems,
			getOrder,
			getOrders,
		})),
		itemServiceMock: vi.fn(() => ({
			getAllItems,
		})),
	};
});

vi.mock("../models/Table/TableService", () => ({
	TableService: mocks.tableService,
}));

vi.mock("../models/Order/OrderService", () => ({
	OrderService: mocks.orderServiceMock,
}));

vi.mock("../models/Item/ItemService", () => ({
	ItemService: mocks.itemServiceMock,
}));

vi.mock("../models/PaymentMethod/PaymentMethodService", () => ({
	PaymentMethodService: mocks.paymentMethodService,
}));

describe("TablesBeingAttendedListPresenter", () => {
	let presenter: TablesBeingAttendedListPresenter;

	beforeEach(() => {
		presenter = new TablesBeingAttendedListPresenter(mocks.viewMock);
		vi.clearAllMocks();
	});

	it("should be defined", () => {
		expect(presenter).toBeDefined();
	});

	it("should show an error when something goes wrong getting orders", async () => {
		mocks.getOrder.mockImplementationOnce(() => {
			throw new Error("error");
		});

		await presenter.getOrder(1);

		expect(mocks.viewMock.onError).toHaveBeenCalledTimes(1);
	});

	it("should get order correctly", async () => {
		await presenter.getOrder(1);

		expect(mocks.getOrder).toHaveBeenCalledTimes(1);
	});

	it("should show an error message something went wrong when add items", async () => {
		mocks.addItems.mockImplementationOnce(() => {
			throw new Error("error");
		});

		await presenter.addConsumptions({
			orderId: 1,
			items: [],
		});

		expect(mocks.addItems).toHaveBeenCalledTimes(1);
		expect(mocks.viewMock.onError).toHaveBeenCalledTimes(1);
	});

	it("should add items to the order correctly", async () => {
		await presenter.addConsumptions({
			orderId: 1,
			items: [],
		});

		expect(mocks.addItems).toHaveBeenCalledTimes(1);
		expect(mocks.viewMock.onSuccess).toHaveBeenCalledTimes(1);
	});

	it("should an error message something went wrong when get all tables being attended", async () => {
		mocks.findAll.mockImplementationOnce(() => {
			throw new Error("error");
		});
		mocks.getAllItems.mockReturnValueOnce({ count: 0, data: [] });
		mocks.getOrders.mockReturnValueOnce([]);

		await presenter.getTablesBeingAttended();

		expect(mocks.findAll).toHaveBeenCalledTimes(1);
		expect(mocks.viewMock.onError).toHaveBeenCalledTimes(1);
	});

	it("should all tables being attended correctly", async () => {
		mocks.findAll.mockReturnValueOnce([]);
		mocks.getAllItems.mockReturnValueOnce({ count: 0, data: [] });
		mocks.getOrders.mockReturnValueOnce([]);

		await presenter.getTablesBeingAttended();

		expect(mocks.findAll).toHaveBeenCalledTimes(1);
		expect(mocks.showTablesBeingAttended).toHaveBeenCalledTimes(1);
		expect(mocks.getAllItems).toHaveBeenCalledTimes(1);
		expect(mocks.getOrders).toHaveBeenCalledTimes(1);
	});

	it("should show error correctly when create order", async () => {
		mocks.createOrder.mockImplementationOnce(() => {
			throw new Error("error");
		});

		await presenter.createOrder({
			clientName: "client one",
			tableId: 1,
		});

		expect(mocks.createOrder).toHaveBeenCalledTimes(1);
		expect(mocks.viewMock.onError).toHaveBeenCalledTimes(1);
	});

	it("should create order correctly", async () => {
		mocks.createOrder.mockReturnValueOnce({ id: 1 });

		await presenter.createOrder({
			clientName: "client one",
			tableId: 1,
		});

		expect(mocks.createOrder).toHaveBeenCalledTimes(1);
		expect(mocks.viewMock.onSuccess).toHaveBeenCalledTimes(1);
	});

	it("should show error correctly when creating order", async () => {
		mocks.createOrder.mockImplementationOnce(() => {
			throw new BadRequestException("Error ao criar pedido");
		});

		await presenter.createOrder({
			clientName: "client one",
			tableId: 1,
		});

		expect(mocks.createOrder).toHaveBeenCalledTimes(1);
	});
});
