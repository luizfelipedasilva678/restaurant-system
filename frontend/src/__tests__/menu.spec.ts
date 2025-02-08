import { fireEvent, getByTestId } from "@testing-library/dom";
import { describe, expect, it } from "vitest";
import { Menu } from "../lib/menu";

describe("menu", () => {
	const menuHtml = `
    <button class="menu__trigger" data-testid="menu-trigger"></button>
    <aside class="menu" data-testid="menu">
      <div class="menu__header">
        <button class="menu__close--button" data-testid="menu-close">✖</button>
      </div>
      <div class="menu__content" data-testid="menu-content"></div>
    </aside>
    <div class="menu__overlay" data-testid="menu-overlay"></div>
  `;

	it("should throw an exception when some menu component is not in the DOM", () => {
		expect(() => new Menu()).toThrowError("Menu not found");

		document.body.innerHTML = `<div class="menu"></div>`;

		expect(() => new Menu()).toThrowError("Menu trigger not found");

		document.body.innerHTML = `<div class="menu"><button class="menu__trigger"></button></div>`;

		expect(() => new Menu()).toThrowError("Menu overlay not found");
	});

	it("should close the menu on 'close-menu' event", () => {
		document.body.innerHTML = menuHtml;

		dispatchEvent(new CustomEvent("close-menu"));

		expect(getByTestId(document.body, "menu")).not.toHaveClass("open");
	});

	it("should open the menu correctly", () => {
		document.body.innerHTML = menuHtml;

		new Menu().setup();

		fireEvent.click(getByTestId(document.body, "menu-trigger"));

		expect(document.documentElement).toHaveStyle({ overflow: "hidden" });
		expect(getByTestId(document.body, "menu")).toHaveClass("menu__open");
		expect(getByTestId(document.body, "menu-overlay")).toHaveClass(
			"menu__overlay--open",
		);
	});

	it("should close the menu correctly", () => {
		document.body.innerHTML = menuHtml;

		new Menu().setup();

		fireEvent.click(getByTestId(document.body, "menu-close"));

		expect(document.documentElement).toHaveStyle({ overflow: "auto" });
		expect(getByTestId(document.body, "menu")).not.toHaveClass("menu__open");
		expect(getByTestId(document.body, "menu-overlay")).not.toHaveClass(
			"menu__overlay--open",
		);
	});
});
