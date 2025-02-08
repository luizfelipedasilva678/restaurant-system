import type { GlobalMiddleware } from "../lib/router";
import { getSession } from "../lib/sessionUtils";

export const authMiddleware: GlobalMiddleware = async () => {
	const currentUrl = new URL(window.location.href);
	const data = getSession();

	if (data) {
		return;
	}

	if (["/login"].includes(currentUrl.pathname)) {
		return;
	}

	currentUrl.pathname = "/login";

	window.location.assign(currentUrl);
};
