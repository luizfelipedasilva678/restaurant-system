export function each<T>(items: T[], cb: (_: T) => string) {
	const list: string[] = [];

	for (const item of items) {
		list.push(cb(item));
	}

	return list.join("");
}
