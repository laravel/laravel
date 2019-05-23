import {
	watchViewport,
} from 'tornis';

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
