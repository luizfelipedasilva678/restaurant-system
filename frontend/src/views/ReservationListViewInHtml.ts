import { emptyReservationList } from "../components/emptyReservationList";
import { reservationList } from "../components/reservationList";
import { PAGE_SIZE } from "../constants";
import type { Context } from "../lib/router";
import type { GetReservationsResponse } from "../models/Reservation/ReservationService";
import { ReservationListPresenter } from "../presenters/ReservationListPresenter";
import { BaseView } from "./BaseView";

export class ReservationListViewInHtml
	extends BaseView
	implements ReservationListView
{
	private readonly presenter: ReservationListPresenter;

	constructor(context: Context) {
		super(context);
		this.presenter = new ReservationListPresenter(this);
	}

	public showReservationsList({
		data: reservations,
		count,
	}: GetReservationsResponse) {
		const { page, size } = this.getPaginationFromContext();

		const totalPages = Math.ceil(count / size);
		const isFirstPage = page === 1;
		const isLastPage = page === totalPages;
		const showPagination = size < count;

		const nextPageUrl = isLastPage ? null : new URL(window.location.href);

		nextPageUrl?.searchParams.set("page", String(page + 1));
		nextPageUrl?.searchParams.set("size", String(size));

		const prevPageUrl = isFirstPage ? null : new URL(window.location.href);

		prevPageUrl?.searchParams.set("page", String(page - 1));
		prevPageUrl?.searchParams.set("size", String(size));

		this.template.innerHTML = reservationList.render({
			nextPageUrl,
			prevPageUrl,
			reservations,
			showPagination,
		});

		const cards = this.template.content.querySelectorAll(".card");

		for (const card of cards) {
			const reservationId = card.getAttribute("data-reservation-id");

			const cancelTrigger = card.querySelector<HTMLButtonElement>(
				"[data-cancel-trigger]",
			);

			if (!cancelTrigger) {
				continue;
			}

			const cancelModal = card.querySelector(
				"[data-cancel-modal]",
			) as HTMLDialogElement;

			const cancelReservationTrigger = cancelModal.querySelector(
				"#cancel-reservation-trigger",
			) as HTMLButtonElement;

			const els = cancelModal.querySelectorAll(
				".modal-background, .modal-close, [data-abort-cancel]",
			);

			for (const el of els) {
				el.addEventListener("click", () =>
					cancelModal.classList.remove("is-active"),
				);
			}

			cancelTrigger.addEventListener("click", () =>
				cancelModal.classList.add("is-active"),
			);

			cancelReservationTrigger.addEventListener("click", () => {
				this.presenter.cancelReservation(String(reservationId));
			});
		}

		this.context.render(this.template.content);
	}

	public findAll = async () => {
		const { page, size } = this.getPaginationFromContext();

		this.presenter.listReservations(page, size);
	};

	public showReservationsEmptyList() {
		this.template.innerHTML = emptyReservationList.render();

		this.context.render(this.template.content);
	}

	private getPaginationFromContext() {
		const { searchParams } = this.context;

		const page = Number(searchParams.get("page") ?? 1);
		const size = Number(searchParams.get("size") ?? PAGE_SIZE);

		return {
			page,
			size,
		};
	}
}
