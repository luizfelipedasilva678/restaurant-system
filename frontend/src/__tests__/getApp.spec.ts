import { describe, expect, it } from "vitest";
import { getApp } from "../lib/getApp";

describe("getApp", () => {
	it("should create the app correctly", () => {
		const app = getApp();

		expect(app).toBeDefined();
		expect(app).toBeInstanceOf(HTMLElement);
	});
});
