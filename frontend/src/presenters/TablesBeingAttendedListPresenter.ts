import { BadRequestException } from "../exceptions/bad-request-exception";
import { ForbiddenException } from "../exceptions/forbidden-exception";
import { getSession } from "../lib/sessionUtils";
import { ItemService } from "../models/Item/ItemService";
import type { AddItemsDTO, OrderCreationDTO } from "../models/Order/Order";
import { OrderService } from "../models/Order/OrderService";
import { PaymentMethodService } from "../models/PaymentMethod/PaymentMethodService";
import { TableService } from "../models/Table/TableService";

export class TablesBeingAttendedListPresenter {
	private readonly itemService: ItemService;
	private readonly orderService: OrderService;
	private readonly tableService: TableService;
	private readonly paymentMethodService: PaymentMethodService;
	private readonly view: TablesBeingAttendView;

	constructor(view: TablesBeingAttendView) {
		this.view = view;

		this.itemService = new ItemService();
		this.orderService = new OrderService();
		this.tableService = new TableService();
		this.paymentMethodService = new PaymentMethodService();
	}

	public createOrder = async (orderCreationDTO: OrderCreationDTO) => {
		try {
			await this.orderService.createOrder(orderCreationDTO);
			this.view.onSuccess("Pedido criado com sucesso");
		} catch (err) {
			this.treatErrorException(err);
		}
	};

	public addConsumptions = async (orderDto: AddItemsDTO) => {
		try {
			await this.orderService.addItems(orderDto);
			this.view.onSuccess("Itens adicionados com sucesso");
		} catch (err) {
			this.view.onError("Erro ao adicionar itens");
		}
	};

	public getOrder = async (id: number) => {
		try {
			return await this.orderService.getOrder(id);
		} catch (err) {
			this.view.onError("Erro ao obter dados");
			return null;
		}
	};

	public fulfill = async ({
		orderId,
		paymentMethodId,
		total,
		discount,
	}: {
		orderId: string;
		paymentMethodId: string;
		total: string;
		discount: string;
	}) => {
		try {
			const employeeId = getSession()?.session?.user.id;

			await this.orderService.fulfillOrder({
				discount: Number(discount ?? 0),
				employeeId: Number(employeeId),
				orderId: Number(orderId),
				paymentMethodId: Number(paymentMethodId),
				total: Number(total),
			});

			this.view.onSuccess("Pedido finalizado com sucesso");
		} catch (error) {
			if (error instanceof ForbiddenException) {
				this.view.onError(error.message);

				return;
			}

			this.view.onError("Erro ao finalizar pedido");
		}
	};

	public getTablesBeingAttended = async () => {
		try {
			const { data: items } = await this.itemService.getAllItems();
			const orders = await this.orderService.getOrders();
			const tables = await this.tableService.findAll();
			const paymentsMethods = await this.paymentMethodService.findAll();

			this.view.showTablesBeingAttended(orders, items, tables, paymentsMethods);
		} catch (err) {
			this.view.onError("Erro ao obter dados");
		}
	};

	private treatErrorException(err: unknown) {
		if (err instanceof BadRequestException) {
			const { errorMessage, errorsMessages } = err;

			if (errorMessage) {
				this.view.onError(errorMessage);

				return;
			}

			for (const msg of errorsMessages) {
				this.view.onError(msg);
			}

			return;
		}

		this.view.onError("Erro ao executar tarefa");
	}
}
