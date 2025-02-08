export function renderIf(condition: boolean, html: string) {
	if (condition) {
		return html;
	}

	return "";
}
