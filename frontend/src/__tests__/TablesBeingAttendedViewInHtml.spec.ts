import { fireEvent } from "@testing-library/dom";
import { beforeEach, describe, expect, it, vi } from "vitest";
import { push } from "../lib/push";
import type { Order } from "../models/Order/Order";
import type { PaymentMethod } from "../models/PaymentMethod/PaymentMethod";
import { TablesBeingAttendedViewInHtml } from "../views/TablesBeingAttendedViewInHtml";

const paymentMethodsMock: PaymentMethod[] = [];

const tablesMock = [
	{
		id: 1,
		number: 1,
	},
];

const itemsMock = [
	{
		id: 1,
		description: "item one",
		code: "E2",
		price: 30,
		category: "category one",
	},
];

const ordersMock: Order[] = [
	{
		id: 1,
		status: "open",
		table: {
			id: 1,
			number: 1,
		},
		client: {
			id: 1,
			name: "client one",
		},
		items: [
			{
				id: 1,
				itemId: 1,
				quantity: 2,
				price: 30,
				description: "item one",
				category: "category one",
			},
		],
	},
];

const mocks = vi.hoisted(() => {
	const createOrder = vi.fn();
	const addConsumptions = vi.fn();
	const getOrder = vi.fn();
	const getTablesBeingAttended = vi.fn();

	return {
		createOrder,
		addConsumptions,
		getOrder,
		getTablesBeingAttended,
		presenterMock: vi.fn(() => ({
			createOrder,
			addConsumptions,
			getOrder,
			getTablesBeingAttended,
		})),
		toastifyMock: vi.fn(),
	};
});

vi.mock("toastify-js", { spy: true });

vi.mock("../presenters/TablesBeingAttendedListPresenter", () => ({
	TablesBeingAttendedListPresenter: mocks.presenterMock,
}));

describe("TablesBeingAttendedViewInHtml", () => {
	let view: TablesBeingAttendedViewInHtml;

	beforeEach(() => {
		view = new TablesBeingAttendedViewInHtml({
			currentPage: "test",
			params: {},
			push: push,
			render: (fragment: DocumentFragment) => {
				document.body.appendChild(fragment);
			},
			searchParams: new URLSearchParams(),
		});

		vi.clearAllMocks();
	});

	it("should set the template correctly", async () => {
		const renderMock = vi.fn();
		view.context.render = renderMock;

		await view.showTablesBeingAttended(
			ordersMock,
			itemsMock,
			tablesMock,
			paymentMethodsMock,
		);

		expect(view.template.innerHTML.includes("tables-being-attended")).toBe(
			true,
		);
		expect(renderMock).toHaveBeenCalledWith(view.template.content);
	});

	it("should create order correctly", async () => {
		await view.showTablesBeingAttended(
			ordersMock,
			itemsMock,
			tablesMock,
			paymentMethodsMock,
		);

		const reportValidityMock = vi.fn();

		const form = document.getElementById(
			"order-creation-form",
		) as HTMLFormElement;

		fireEvent.submit(form, {
			target: {
				reportValidity: reportValidityMock,
				"table-id": {
					value: 1,
				},
				"client-name": {
					value: "João PEdro",
				},
			},
			preventDefault: () => {},
		});

		expect(form).toBeInTheDocument();
		expect(reportValidityMock).toBeCalledTimes(1);
		expect(mocks.createOrder).toBeCalledTimes(1);
	});

	it("should not call presenter when reportValidity fails", async () => {
		await view.showTablesBeingAttended(
			ordersMock,
			itemsMock,
			tablesMock,
			paymentMethodsMock,
		);

		const reportValidityMock = vi.fn(() => false);

		const form = document.getElementById(
			"order-creation-form",
		) as HTMLFormElement;

		fireEvent.submit(form, {
			target: {
				reportValidity: reportValidityMock,
			},
			preventDefault: () => {},
		});

		expect(form).toBeInTheDocument();
		expect(reportValidityMock).toBeCalledTimes(1);
		expect(mocks.createOrder).not.toBeCalled();
	});

	it("should addConsumptions correctly", async () => {
		await view.showTablesBeingAttended(
			ordersMock,
			itemsMock,
			tablesMock,
			paymentMethodsMock,
		);

		const form = document.getElementById(
			"consumptions-form",
		) as HTMLFormElement;

		fireEvent.submit(form, {
			preventDefault: () => {},
		});

		expect(mocks.addConsumptions).toBeCalled();
	});
});
