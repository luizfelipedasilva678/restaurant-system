export function template(html: TemplateStringsArray) {
	const templateTag = document.createElement("template");

	templateTag.innerHTML = html.join("");

	return templateTag.content;
}
