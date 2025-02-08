import type { Component } from "./component";

export const loginForm: Component = {
	render: () => {
		return String.raw`
      <form class="is-flex is-flex-direction-column mt-6 login__form" id="login-form" data-testid="login-form">
        <div class="field">
          <label class="label" for="name">Login</label>
          <div class="control">
            <input class="input" type="text" placeholder="Digite o seu login" name="login" id="login" required="true" data-testid="login-field">
          </div>
        </div>

        <div class="field">
          <label class="label" for="name">Senha</label>
          <div class="control">
            <input class="input" type="password" placeholder="Digite a senha" name="password" id="password" required="true" data-testid="password-field">
          </div>
        </div>

        <div class="field is-grouped">
          <div class="control">
            <button class="button is-link" id="login-button" type="submit" data-testid="login-button">Acessar o sistema</button>
            <button class="button is-link is-light" type="reset" data-testid="cancel-login-button">Cancelar</button>
          </div>
        </div>
      </form>
    `;
	},
};
