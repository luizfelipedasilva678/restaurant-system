export function initDateInputs(
	initialDateInput: HTMLInputElement,
	finalDateInput: HTMLInputElement,
) {
	const date = new Date();
	const initialDay = new Date(date.getFullYear(), date.getMonth(), 1);
	const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

	initialDateInput.value = initialDay.toISOString().split("T")[0];
	finalDateInput.value = lastDay.toISOString().split("T")[0];
}
