import {
	watchViewport,
} from 'tornis/dist/tornis.es5';

let x = 0,
    y = 0;

const updateValues = ({
    scroll,
}) => {
    if (scroll.changed) {
		x = scroll.left;
		y = scroll.top;
    }
};

watchViewport(updateValues);

export const getX = () => x;

export const getY = () => y;
