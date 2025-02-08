import { __SESSION__ } from "../constants";
import type { AuthDTO } from "../models/Auth/Auth";
import { storageInSession } from "./storage";

export function getSession() {
	return storageInSession.get<AuthDTO>(__SESSION__);
}

export function setSession(session: AuthDTO) {
	return storageInSession.set(__SESSION__, session);
}

export function deleteSession() {
	storageInSession.del(__SESSION__);
}
