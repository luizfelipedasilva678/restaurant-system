import { BASE_API_URL } from "../../constants";
import { request } from "../../lib/request";
import type { Reservation, ReservationDTO } from "./Reservation";

export interface GetReservationsResponse {
	data: Reservation[];
	count: number;
}

export class ReservationService {
	async findAll(page = 1, size = 10) {
		return request<GetReservationsResponse>(
			`${BASE_API_URL}/reservations?page=${page}&perPage=${size}`,
		);
	}

	async create(dto: ReservationDTO) {
		return request<Reservation>(`${BASE_API_URL}/reservations`, {
			method: "POST",
			headers: {
				"Content-type": "application/json",
			},
			body: JSON.stringify(dto),
		});
	}

	async cancelReservation(id: string) {
		return request(`${BASE_API_URL}/reservations/${id}`, {
			method: "PATCH",
			headers: {
				"Content-type": "application/json",
			},
			body: JSON.stringify({
				status: "inactive",
			}),
		});
	}

	async getReportData(initialDate: string, finalDate: string) {
		try {
			const page = 1;
			const perPage = 10000;
			const promises: Promise<GetReservationsResponse>[] = [];
			const { count, data } = await request<GetReservationsResponse>(
				`${BASE_API_URL}/reservations?initialDate=${initialDate}&finalDate=${finalDate}&page=${page}&perPage=${perPage}`,
			);
			const totalPages = Math.ceil(count / perPage);

			if (totalPages === 1) {
				return this.sumReservationsByDate(data);
			}

			for (let i = 2; i <= totalPages; i++) {
				promises.push(
					request<GetReservationsResponse>(
						`${BASE_API_URL}/reservations?initialDate=${initialDate}&finalDate=${finalDate}&page=${i}&perPage=${perPage}`,
					),
				);
			}

			const responses = await Promise.all(promises);

			return this.sumReservationsByDate(
				data.concat(...responses.flatMap((r) => r.data)),
			);
		} catch (_) {
			return {};
		}
	}

	private sumReservationsByDate(reservations: Reservation[]) {
		const grouped: Record<string, number> = {};

		for (const reservation of reservations) {
			const date = reservation.startTime.split(" ")[0];

			if (date in grouped) {
				grouped[date] += 1;
			} else {
				grouped[date] = 1;
			}
		}

		return grouped;
	}
}
