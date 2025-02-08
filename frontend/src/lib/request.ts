import { BadRequestException } from "../exceptions/bad-request-exception";
import { ForbiddenException } from "../exceptions/forbidden-exception";
import { InternalRequestError } from "../exceptions/internal-request-error-exception";
import { deleteSession } from "./sessionUtils";

interface RequestHooks {
	onBadRequest?<T extends Record<string, unknown>>(_: T): void;
	onUnauthorized?<T extends Record<string, unknown>>(_: T): void;
	onForbidden?<T extends Record<string, unknown>>(_: T): void;
}

const onBadRequestDefault: RequestHooks["onBadRequest"] = (payload) => {
	const { message, messages } = payload;

	if (message && typeof message === "string") {
		throw new BadRequestException("Invalid request data", [], message);
	}

	if (messages && Array.isArray(messages)) {
		throw new BadRequestException("Invalid request data", messages);
	}
};

const onForbiddenDefault: RequestHooks["onForbidden"] = () => {
	throw new ForbiddenException(
		"O usuário atual não tem acesso para acessar esse recurso",
	);
};

const onUnauthorizedDefault: RequestHooks["onUnauthorized"] = () => {
	const currentUrl = new URL(window.location.href);

	if (["/login"].includes(currentUrl.pathname)) {
		return;
	}

	deleteSession();

	currentUrl.pathname = "/login";

	window.location.assign(currentUrl);
};

export async function request<T>(
	url: string,
	init?: RequestInit,
	hooks?: RequestHooks,
): Promise<T> {
	const { onBadRequest, onForbidden, onUnauthorized } = hooks ?? {
		onBadRequest: onBadRequestDefault,
		onForbidden: onForbiddenDefault,
		onUnauthorized: onUnauthorizedDefault,
	};

	const req = await fetch(url, { credentials: "include", ...init });

	const payload = await req.json();

	const isBadRequest = req.status === 400;
	const isUnauthorized = req.status === 401;
	const isForbidden = req.status === 403;

	if (isBadRequest) {
		onBadRequest?.(payload);
	}

	if (isUnauthorized) {
		onUnauthorized?.(payload);
	}

	if (isForbidden) {
		onForbidden?.(payload);
	}

	if (!req.ok) {
		throw new InternalRequestError("Internal request error");
	}

	return payload;
}
