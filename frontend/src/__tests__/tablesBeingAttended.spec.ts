import { beforeEach, describe, expect, it } from "vitest";
import { tablesBeingAttended } from "../components/tablesBeingAttended";

describe("tablesBeingAttended", () => {
	beforeEach(() => {
		document.body.innerHTML = tablesBeingAttended.render({
			items: [],
			orders: [],
			tables: [],
			paymentsMethods: [],
		});
	});

	it("should render the component correctly", () => {
		expect(document.getElementById("tables-being-attended")).not.toBeNull();
		expect(document.getElementById("create-order-button")).not.toBeNull();
		expect(document.getElementById("order-creation-modal")).not.toBeNull();
	});

	it("should render the payments methods list", () => {
		expect(document.getElementById("order-fulfill-modal")).not.toBeNull();
		expect(document.getElementById("fulfill-order")).not.toBeNull();
		expect(document.getElementById("payment-method-id")).not.toBeNull();
	});
});
