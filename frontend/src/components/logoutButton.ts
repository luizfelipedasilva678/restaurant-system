import type { Component } from "./component";

export const logoutButton: Component = {
	render: () => {
		return String.raw`
      <button class="has-text-danger is-flex is-size-6" id="logout-button" data-testid="logout-button">
        <svg class="mr-2" height="18" width="18" xmlns="http://www.w3.org/2000/svg" fil="" viewBox="0 0 384.971 384.971" xml:space="preserve">
          <path fill="hsl(348, 100%, 61%)" d="M180.455 360.91H24.061V24.061h156.394c6.641 0 12.03-5.39 12.03-12.03s-5.39-12.03-12.03-12.03H12.03C5.39.001 0 5.39 0 12.031V372.94c0 6.641 5.39 12.03 12.03 12.03h168.424c6.641 0 12.03-5.39 12.03-12.03.001-6.641-5.389-12.03-12.029-12.03"/>
          <path fill="hsl(348, 100%, 61%)" d="m381.481 184.088-83.009-84.2a11.94 11.94 0 0 0-17.011 0c-4.704 4.74-4.704 12.439 0 17.179l62.558 63.46H96.279c-6.641 0-12.03 5.438-12.03 12.151s5.39 12.151 12.03 12.151h247.74l-62.558 63.46c-4.704 4.752-4.704 12.439 0 17.179a11.93 11.93 0 0 0 17.011 0l82.997-84.2c4.644-4.68 4.692-12.512.012-17.18"/>
          <g/>
          <g/>
          <g/>
          <g/>
          <g/>
          <g/>
        </svg>

        Sair
      </button>
    `;
	},
};
