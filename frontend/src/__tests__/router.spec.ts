import { fireEvent, getByTestId } from "@testing-library/dom";
import "@testing-library/jest-dom";
import { beforeEach, describe, expect, it, vi } from "vitest";
import { getApp } from "../lib/getApp";
import { Router } from "../lib/router";
import { template } from "../lib/template";

describe("Router", () => {
	let app: HTMLElement;
	let router: Router;

	beforeEach(() => {
		app = getApp();
		router = new Router(app);

		document.body.appendChild(app);

		router.init();

		history.pushState({}, "", "/");
	});

	it("should call the middlewares correctly", () => {
		const m1 = vi.fn();

		router.use(m1);

		router.path("/", async (ctx) => {
			ctx.render(template`
        <div data-testid='content'>
          <h1 data-testid='title'>Hello world</h1>
        </div>
      `);
		});

		fireEvent.load(window);

		expect(m1).toHaveBeenCalled();
	});

	it("should add a new route correctly", () => {
		router.path("/", async (ctx) => {
			ctx.render(template`
        <div data-testid='content'>
          <h1 data-testid='title'>Hello world</h1>
        </div>
      `);
		});

		fireEvent.load(window);

		expect(getByTestId(app, "content")).toBeInTheDocument();
		expect(getByTestId(app, "title")).toBeInTheDocument();
	});

	it("should set the path params correctly", () => {
		router.path("/product/:id", async (ctx) => {
			ctx.render(template`
        <div data-testid='content'>
          <h1 data-testid='title'>Product page</h1>
        </div>  
      `);

			expect(ctx.params).toEqual({ id: "1" });
		});

		fireEvent.load(window);

		history.pushState({}, "", "/product/1");
	});

	it("should navigate to another route correctly", () => {
		router.path("/", async (ctx) => {
			ctx.render(template`
        <div data-testid='content'>
          <a href='/test' data-router-link data-testid='link'>go to page 2</a>
        </div>
      `);
		});

		router.path("/test", async (ctx) => {
			ctx.render(template`
        <div data-testid='content'>
          <h1 data-testid='title'>Page 2</h1>
        </div>
      `);
		});

		fireEvent.load(window);

		fireEvent.click(getByTestId(app, "link"));

		expect(getByTestId(app, "content")).toBeDefined();
		expect(getByTestId(app, "title")).toHaveTextContent("Page 2");
	});
});
