import { describe, expect, it } from "vitest";
import { setupMenuLinks } from "../lib/setupMenuLinks";

describe("setupMenuLinks", () => {
	it("should setup the menu links correctly", () => {
		document.body.innerHTML = `
      <div class="menu"> <ul> </ul> </div>
    `;

		setupMenuLinks();

		expect(document.querySelectorAll(".menu li").length).toBe(4);
	});
});
