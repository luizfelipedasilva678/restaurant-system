import { UnauthorizedException } from "../exceptions/unauthorized-exception";
import { deleteSession, setSession } from "../lib/sessionUtils";
import { AuthService } from "../models/Auth/AuthService";

export class AuthPresenter {
	private readonly service: AuthService;
	private readonly view: AuthView;

	constructor(view: AuthView) {
		this.view = view;
		this.service = new AuthService();
	}

	public async getCurrentSession() {
		try {
			const session = await this.service.findSession();

			if (session) {
				return session;
			}

			return null;
		} catch (error) {
			return null;
		}
	}

	public async logout() {
		try {
			await this.service.logout();

			deleteSession();

			this.view.onSuccess("Sessão finalizada com sucesso");
		} catch (error) {
			this.view.onError("Erro ao finalizar sessão");
		}
	}

	public async login(login: string, password: string) {
		try {
			await this.service.login(login, password);

			const session = await this.getCurrentSession();

			session && setSession(session);

			this.view.onSuccess("Login realizado com sucesso");
		} catch (error) {
			if (error instanceof UnauthorizedException) {
				this.view.onError("Usuário ou senha inválidos");

				return;
			}

			this.view.onError("Erro ao autenticar usuário");
		}
	}
}
