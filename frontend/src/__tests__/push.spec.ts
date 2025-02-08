import { describe, expect, it } from "vitest";
import { push } from "../lib/push";

describe("push", () => {
	it.each([
		["/test", "/test"],
		["/test-one", "/test-one"],
		["/test-two", "/test-two"],
	])("should navigate to %s correctly", (path, expected) => {
		push(path);

		expect(window.location.pathname).toBe(expected);
	});
});
