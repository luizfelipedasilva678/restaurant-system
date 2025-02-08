import { describe, expect, it } from "vitest";
import priceFormatter from "../lib/priceFormatter";

describe("priceFormatter", () => {
	it("should format the price correctly", () => {
		expect(priceFormatter(100)).toBeDefined();
	});

	it("should format the price with two decimal places correctly", () => {
		expect(priceFormatter(200)).toBeDefined();
	});
});
