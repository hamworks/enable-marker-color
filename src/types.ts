export type BlockAttributes = {
	markerColor: string;
};

export type WithColorProps = {
	markerColor: {
		color: string;
	};
	setMarkerColor: ( color: string ) => void;
};
