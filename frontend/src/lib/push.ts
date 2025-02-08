export function push(url: string | URL) {
	history.pushState({}, "", url);

	dispatchEvent(new CustomEvent("pathstate"));
}
