export function setupMenuLinks() {
	const menuLinks = document.querySelector<HTMLUListElement>(".menu ul");

	if (!menuLinks) return;

	const lis: HTMLLIElement[] = [];
	const linksConfigs = [
		{
			label: "Relatório de vendas por categoria",
			href: "/sales-by-category",
		},
		{
			label: "Relatório de vendas por método de pagamento",
			href: "/sales-by-payment-method",
		},
		{
			label: "Relatório de vendas por funcionário",
			href: "/sales-by-employee",
		},
		{
			label: "Relatório de vendas por dia",
			href: "/sales-by-date",
		},
	];

	for (let i = 0; i < 4; i++) {
		const li = document.createElement("li");
		const a = document.createElement("a");
		a.classList.add("button", "is-white");
		a.setAttribute("data-router-link", "true");
		a.innerText = linksConfigs[i].label;
		a.href = linksConfigs[i].href;
		li.classList.add("mb-3");
		li.appendChild(a);
		lis.push(li);
	}

	menuLinks.append(...lis);
}
