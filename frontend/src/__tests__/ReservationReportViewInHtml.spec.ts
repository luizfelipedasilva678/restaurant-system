import { fireEvent, getByTestId } from "@testing-library/dom";
import Toastify from "toastify-js";
import { beforeEach, describe, expect, it, vi } from "vitest";
import { push } from "../lib/push";
import { ReservationReportViewInHtml } from "../views/ReservationReportViewInHtml";

const mocks = vi.hoisted(() => {
	const getReportData = vi.fn(() => ({
		"2024-12-07": 7,
		"2024-12-08": 9,
		"2024-12-09": 5,
		"2024-12-10": 10,
	}));

	return {
		presenterMock: vi.fn(() => ({
			getReportData,
		})),
		toastifyMock: vi.fn(),
	};
});

vi.mock("../presenters/ReservationReportPresenter", () => ({
	ReservationReportPresenter: mocks.presenterMock,
}));

vi.mock("toastify-js", { spy: true });

vi.mock("chart.js");

describe("ReservationReportViewInHtml.spec", () => {
	let view: ReservationReportViewInHtml;

	beforeEach(() => {
		vi.clearAllMocks();

		document.body.innerHTML = "";

		view = new ReservationReportViewInHtml({
			currentPage: "test",
			params: {},
			push: push,
			render: (fragment: DocumentFragment) => {
				document.body.appendChild(fragment);
			},
			searchParams: new URLSearchParams(),
		});
	});

	it("should show a toast given that an error occurs", async () => {
		await view.report();

		const form = getByTestId(
			document.body,
			"get-report-form",
		) as HTMLFormElement;
		const mockReportValidity = vi.fn(() => false);

		fireEvent.submit(form, {
			target: {
				"initial-date": { value: "2024-12-06" },
				"final-date": { value: "2024-12-07" },
				reportValidity: mockReportValidity,
			},
		});

		expect(Toastify).toHaveBeenCalled();
		expect(form).toBeInTheDocument();
		expect(mockReportValidity).toHaveBeenCalled();
	});

	it("should generate report on form submit", async () => {
		await view.report();

		const form = getByTestId(
			document.body,
			"get-report-form",
		) as HTMLFormElement;
		const mockReportValidity = vi.fn();

		fireEvent.submit(form, {
			target: {
				"initial-date": { value: "2024-12-06" },
				"final-date": { value: "2024-12-07" },
				reportValidity: mockReportValidity,
			},
		});

		expect(form).toBeInTheDocument();
		expect(mockReportValidity).toHaveBeenCalled();
		expect(mocks.presenterMock().getReportData).toHaveBeenCalled();
	});

	it("should render template for get report correctly", async () => {
		const renderMock = vi.fn();
		view.context.render = renderMock;

		await view.report();

		expect(view.template.innerHTML.includes("get-report-form")).toBe(true);
		expect(view.template.innerHTML.includes("initial-date")).toBe(true);
		expect(view.template.innerHTML.includes("final-date")).toBe(true);
		expect(view.template.innerHTML.includes("report-container")).toBe(true);
		expect(renderMock).toHaveBeenCalledWith(view.template.content);
	});

	it("should show a toast when onSuccess is called", () => {
		view.onSuccess("Success message");

		expect(Toastify).toHaveBeenCalledWith({
			className: "has-background-success",
			text: "Success message",
			position: "right",
			style: {
				background:
					"hsl(var(--bulma-success-h), var(--bulma-success-s), var(--bulma-success-l))",
			},
			duration: 3000,
		});
	});

	it("should show a toast when onError is called", () => {
		view.onError("Error message");

		expect(Toastify).toHaveBeenCalledWith({
			text: "Error message",
			position: "right",
			duration: 3000,
			style: {
				background:
					"hsl(var(--bulma-danger-h), var(--bulma-danger-s), var(--bulma-danger-l))",
			},
		});
	});
});
