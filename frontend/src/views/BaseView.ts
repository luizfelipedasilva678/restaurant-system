import Toastify from "toastify-js";
import type { Context } from "../lib/router";

export abstract class BaseView {
	private _context: Context;
	private readonly _template: HTMLTemplateElement;

	constructor(ctx: Context) {
		this.context = ctx;
		this._template = document.createElement("template");
	}

	private set context(ctx: Context) {
		this._context = ctx;
	}

	get context() {
		return this._context;
	}

	get template() {
		return this._template;
	}

	public onError(message: string, duration = 3000) {
		Toastify({
			text: this.treatMessage(message),
			position: "right",
			duration,
			style: {
				background:
					"hsl(var(--bulma-danger-h), var(--bulma-danger-s), var(--bulma-danger-l))",
			},
		}).showToast();
	}

	public onSuccess(
		message: string,
		duration = 3000,
		cb: VoidFunction = () => {
			this._context.push("/");
		},
	) {
		Toastify({
			className: "has-background-success",
			text: message,
			position: "right",
			style: {
				background:
					"hsl(var(--bulma-success-h), var(--bulma-success-s), var(--bulma-success-l))",
			},
			duration,
		}).showToast();

		cb();
	}

	private treatMessage(message: string) {
		const fieldMap: Record<string, string> = {
			tableId: "mesa",
			employeeId: "funcionário responsável",
			startTime: "horário da reserva",
			clientName: "nome do cliente",
			clientPhone: "telefone do cliente",
		};

		const entry = Object.entries(fieldMap).find(([field]) =>
			new RegExp(field, "gi").test(message),
		);

		if (!entry) {
			return message;
		}

		const [field, value] = entry;

		return message.replace(field, value);
	}
}
