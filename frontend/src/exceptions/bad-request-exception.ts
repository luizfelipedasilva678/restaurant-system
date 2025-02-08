export class BadRequestException extends Error {
	private readonly _errorMessage: string | null;
	private readonly _errorsMessages: string[];

	constructor(
		message: string,
		errorMessages: string[] = [],
		errorMessage: string | null = null,
	) {
		super(message);

		this._errorsMessages = errorMessages;
		this._errorMessage = errorMessage;
	}

	get errorsMessages() {
		return this._errorsMessages;
	}

	get errorMessage() {
		return this._errorMessage;
	}
}
