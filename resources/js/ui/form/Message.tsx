import type { ComponentPropsWithoutRef } from "react";

import { tw } from "@/utils";

export interface MessageProps extends ComponentPropsWithoutRef<"p"> {
  message?: string;
  error?: string | boolean;
}

export const Message = ({ message, error, className }: MessageProps) => (
  <p
    className={tw(
      "text-black-800 block pb-1 pt-1 text-left text-xs opacity-80",
      className,

      !!error && "text-red-400",
    )}
  >
    {error === true ? "\u200b" : !error ? message ?? "\u200b" : error}
  </p>
);
