export function classnames(
	classesConfig: Record<string, boolean>,
	...defaultClasses: string[]
) {
	const classes = Object.entries(classesConfig)
		.filter(([, shouldAdd]) => shouldAdd)
		.map(([className]) => className);

	for (const c of defaultClasses) {
		classes.push(c);
	}

	return classes.join(" ").trim();
}
