import type { Menu } from "../lib/menu";
import type { GlobalMiddleware } from "../lib/router";

export const menuMiddleware = (menu: Menu): GlobalMiddleware => {
	return async () => {
		menu.dispatchEvent(new CustomEvent("close-menu"));
	};
};
