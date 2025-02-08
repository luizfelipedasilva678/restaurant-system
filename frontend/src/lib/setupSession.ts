import { AuthService } from "../models/Auth/AuthService";
import { setSession } from "./sessionUtils";

export async function setupSession() {
	try {
		const authService = new AuthService();

		const data = await authService.findSession();

		setSession(data);
	} catch (err) {
		// Nothing to do
	}
}
