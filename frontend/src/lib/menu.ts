export class Menu extends EventTarget {
	private open = false;
	private readonly trigger: HTMLElement;
	private readonly menu: HTMLElement;
	private readonly menuOverlay: HTMLElement;

	constructor() {
		super();

		const menuElement = document.querySelector<HTMLElement>(".menu");

		if (!menuElement) {
			throw new Error("Menu not found");
		}

		const menuTrigger = document.querySelector<HTMLElement>(".menu__trigger");

		if (!menuTrigger) {
			throw new Error("Menu trigger not found");
		}

		const menuOverlay = document.querySelector<HTMLElement>(".menu__overlay");

		if (!menuOverlay) {
			throw new Error("Menu overlay not found");
		}

		this.menu = menuElement;
		this.trigger = menuTrigger;
		this.menuOverlay = menuOverlay;
	}

	public setup() {
		this.addEventListener("close-menu", this.onClose);
		this.trigger.addEventListener("click", this.onTriggerMenu);

		const closeButton = this.menu.querySelector<HTMLButtonElement>(
			".menu__close--button",
		);

		if (closeButton) {
			closeButton.addEventListener("click", this.onClose);
		}
	}

	private readonly onClose = () => {
		this.menu.classList.remove("menu__open");
		this.menuOverlay.classList.remove("menu__overlay--open");

		this.unblockScroll();
	};

	private readonly onTriggerMenu = () => {
		this.open = !this.open;

		if (this.open) {
			this.blockScroll();
		} else {
			this.unblockScroll();
		}

		this.menu.classList.toggle("menu__open");
		this.menuOverlay.classList.toggle("menu__overlay--open");
	};

	private readonly blockScroll = () => {
		document.documentElement.style.setProperty("overflow", "hidden");
	};

	private readonly unblockScroll = () => {
		document.documentElement.style.setProperty("overflow", "auto");
	};
}
