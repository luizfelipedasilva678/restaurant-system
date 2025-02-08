interface Storage {
	get<T = unknown>(key: string): T | null;
	set<T = unknown>(key: string, value: T): T;
	del(key: string): void;
}

export const storageInSession: Storage = {
	get: <T = unknown>(key: string): T | null => {
		const data = sessionStorage.getItem(key);

		if (!data) {
			return null;
		}

		return JSON.parse(data);
	},
	set: <T = unknown>(key: string, value: T): T => {
		sessionStorage.setItem(key, JSON.stringify(value));

		return value;
	},
	del: (key: string) => {
		sessionStorage.removeItem(key);
	},
};
