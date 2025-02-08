export function formatDate(date: string) {
	return new Date(date).toLocaleDateString("pt-BR", {
		hour: "2-digit",
		minute: "2-digit",
	});
}
