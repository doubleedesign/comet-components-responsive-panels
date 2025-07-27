export type PanelTitle = {
	attributes?: Record<string, string>;
	classes?: string[];
	content: string;
};

export type PanelContent = {
	attributes?: Record<string, string>;
	classes?: string[];
	content: string;
};

// Note: This assumes there is an equal number of titles and panels
// and that they are in the correct order to match up
export type PanelItem = {
	summary: {
		attributes?: Record<string, string>;
		classes?: string[];
		title: PanelTitle;
		subtitle?: PanelTitle;
	}
	content: PanelContent;
};
