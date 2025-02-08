import type { User } from "../models/Auth/Auth";

export function setupUserInfo(user: User) {
	const element = document.querySelector(
		".menu__header--user-info",
	) as HTMLParagraphElement;

	element.classList.add("mb-4");
	element.textContent = `Olá, ${user.name}`;
}
