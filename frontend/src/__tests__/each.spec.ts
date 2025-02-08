import { beforeEach, describe, expect, it, vi } from "vitest";
import { each } from "../lib/each";

describe("each", () => {
	const cbMock = vi.fn();

	beforeEach(() => {
		vi.clearAllMocks();
	});

	it("should call the callback for each item on the list", () => {
		each([1, 2, 3, 4, 5], cbMock);

		expect(cbMock).toHaveBeenCalledTimes(5);
	});

	it("should return an string with all items together", () => {
		cbMock.mockImplementation((item) => item);

		const result = each([1, 2, 3, 4, 5], cbMock);

		expect(result).toBe("12345");
	});
});
