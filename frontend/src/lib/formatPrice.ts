export function formatPrice(price: number) {
	return new Intl.NumberFormat("pt-BR", {
		currency: "BRL",
		maximumFractionDigits: 2,
		minimumFractionDigits: 2,
		style: "currency",
	}).format(price);
}
