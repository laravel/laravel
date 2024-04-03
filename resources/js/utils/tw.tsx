import { twMerge } from "tailwind-merge";

/**
 * This is now just a name alias for twMerge, we were using clsx in here too
 * But as it turns out, twMerge by itself is doing exactly what we need
 * It just doesn't support the object api part of clsx and that's actually good
 *
 * @typeParam T - target string
 */
export const tw = twMerge;
