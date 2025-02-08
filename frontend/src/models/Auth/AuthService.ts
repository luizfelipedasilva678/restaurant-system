import { BASE_API_URL } from "../../constants";
import { UnauthorizedException } from "../../exceptions/unauthorized-exception";
import { request } from "../../lib/request";
import type { AuthDTO, AuthSuccessDTO } from "./Auth";

export class AuthService {
	async findSession() {
		return request<AuthDTO>(`${BASE_API_URL}/auth/session`);
	}

	async login(login: string, password: string) {
		return request<AuthSuccessDTO>(
			`${BASE_API_URL}/auth/login`,
			{
				headers: {
					"Content-type": "application/json",
				},
				body: JSON.stringify({
					login,
					password,
				}),
				method: "POST",
			},
			{
				onBadRequest({ message }) {
					throw new UnauthorizedException(String(message));
				},
			},
		);
	}

	async logout() {
		return request<AuthSuccessDTO>(`${BASE_API_URL}/auth/logout`);
	}
}
