import { describe, expect, it } from "vitest";
import { formatDate } from "../lib/formatDate";

describe("formatDate", () => {
	it("should format the date correctly", () => {
		expect(formatDate("2024-12-03 21:52:00")).toBe("03/12/2024, 21:52");
		expect(formatDate("2024-12-04 22:52:00")).toBe("04/12/2024, 22:52");
		expect(formatDate("2024-12-05 00:00:00")).toBe("05/12/2024, 00:00");
		expect(formatDate("2024-12-06 13:52:00")).toBe("06/12/2024, 13:52");
		expect(formatDate("2024-12-07 01:00:00")).toBe("07/12/2024, 01:00");
	});
});
