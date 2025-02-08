import { describe, expect, it } from "vitest";
import { renderIf } from "../lib/renderIf";

describe("renderIf", () => {
	it.each([
		[true, "<div>content</div>", "<div>content</div>"],
		[false, "", ""],
		[true, "<div>content</div>", "<div>content</div>"],
	])(
		"should render the content correctly",
		(conditional, content, expected) => {
			const result = renderIf(conditional, content);

			expect(result).toBe(expected);
		},
	);
});
