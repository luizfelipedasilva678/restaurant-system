import { match, pathToRegexp } from "path-to-regexp";
import { push } from "./push";

type Params = Partial<Record<string, string | string[]>>;

type RouteMiddleware = (_: Context) => Promise<void>;

interface RouterData {
	middleware: RouteMiddleware;
	pathPattern: RegExp;
}

export type GlobalMiddleware = () => Promise<void>;

export interface Context {
	params: Params;
	searchParams: URLSearchParams;
	push: typeof push;
	render(_: DocumentFragment): void;
	currentPage: string;
}

export class Router {
	private readonly notFoundPath = "/404";
	private paths: Record<string, RouterData> = {};
	private readonly globalMiddlewares: GlobalMiddleware[] = [];

	constructor(private readonly app: HTMLElement) {
		this.app = app;
	}

	public use(...middlewares: GlobalMiddleware[]) {
		this.globalMiddlewares.push(...middlewares);
	}

	private getCtx(params: Params, currentPage: string): Context {
		const { searchParams } = new URL(window.location.href);

		return {
			currentPage,
			params,
			searchParams,
			push,
			render: (fragment: DocumentFragment) => {
				this.app.replaceChildren(fragment);
			},
		};
	}

	private async handleRouterChange() {
		let pathFound = false;
		const pathname = window.location.pathname;

		for (const middleware of this.globalMiddlewares) {
			await middleware();
		}

		for (const [path, { pathPattern, middleware }] of Object.entries(
			this.paths,
		)) {
			const matchFn = match(path);
			const matches = matchFn(pathname);

			if (pathPattern.test(pathname) && matches) {
				pathFound = true;
				const { params } = matches;

				middleware(this.getCtx(params, pathname));
			}
		}

		if (pathFound) {
			return;
		}

		if (!(this.notFoundPath in this.paths)) {
			return;
		}

		push(this.notFoundPath);

		const { middleware } = this.paths[this.notFoundPath];

		middleware(this.getCtx({}, pathname));
	}

	private async handleLink(event: MouseEvent) {
		const target = event.target as HTMLElement;

		if (!target.hasAttribute("data-router-link")) {
			return;
		}

		event.preventDefault();

		const link = target as HTMLLinkElement;
		const href = link.href;

		push(href);
	}

	public path(path: string, middleware: RouteMiddleware) {
		this.paths[path] = {
			pathPattern: pathToRegexp(path).regexp,
			middleware,
		};
	}

	public init() {
		window.addEventListener("load", this.handleRouterChange.bind(this));
		window.addEventListener("pathstate", this.handleRouterChange.bind(this));
		window.addEventListener("popstate", this.handleRouterChange.bind(this));
		window.addEventListener("click", this.handleLink.bind(this));
	}
}
