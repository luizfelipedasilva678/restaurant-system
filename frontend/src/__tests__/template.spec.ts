import { describe, expect, it } from "vitest";
import { template } from "../lib/template";

describe("template", () => {
	it("should create a template correctly", () => {
		const html = template`
      <div>content</div>
    `;

		expect(html.firstElementChild).toBeInstanceOf(HTMLDivElement);
	});
});
