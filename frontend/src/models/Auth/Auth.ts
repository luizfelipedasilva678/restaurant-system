export interface AuthDTO {
	session: Session | null;
}

export interface Session {
	user: User;
}

export interface AuthSuccessDTO {
	message: string;
}

export interface User {
	id: number;
	name: string;
	login: string;
	type: "attendant" | "manager";
}
