export interface Component<Props = unknown> {
	render(...props: Props[]): string;
}
