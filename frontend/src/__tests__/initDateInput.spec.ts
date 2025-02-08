import { describe, expect, it, vi } from "vitest";
import { initDateInputs } from "../lib/initDateInput";

describe("initDateInput", () => {
	it("should set the default input value correctly", () => {
		vi.useFakeTimers();

		vi.setSystemTime(new Date(2024, 0, 1));

		const inputOne = document.createElement("input");
		const inputTwo = document.createElement("input");

		initDateInputs(inputOne, inputTwo);

		expect(inputOne).toHaveValue("2024-01-01");
		expect(inputTwo).toHaveValue("2024-01-31");
	});
});
