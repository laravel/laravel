import type { ComponentPropsWithoutRef, ForwardedRef, ReactNode } from "react";

import { forwardRef, tw } from "@/utils";
import { IconWrapper } from "../common";
import { Label } from "./Label";
import { Message } from "./Message";

export interface InputProps extends ComponentPropsWithoutRef<"input"> {
  compact?: boolean;
  containerClassName?: string;
  error?: string | boolean;
  iconClassName?: string;
  id: string;
  label?: ReactNode;
  left?: ReactNode;
  message?: string;
}

export const Input = forwardRef(
  (
    {
      className,
      compact,
      containerClassName,
      error,
      id,
      label,
      left,
      message,
      ...rest
    }: InputProps,
    ref: ForwardedRef<HTMLInputElement>,
  ) => (
    <div className={tw("relative", containerClassName)}>
      {!!label && <Label htmlFor={id} label={label} />}
      <div
        className={tw(
          "flex flex-row items-center rounded-md shadow-sm",
          !!rest.disabled && "opacity-30",
        )}
      >
        {!!left && (
          <div className="pointer-events-none absolute pl-3">
            <IconWrapper size="sm" className="text-gray-400">
              {left}
            </IconWrapper>
          </div>
        )}
        <input
          ref={ref}
          type="text"
          id={id}
          {...rest}
          className={tw(
            "block w-full rounded-md border border-gray-300 bg-gray-800 px-3 py-2 text-sm text-gray-50 placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500",
            !!error && "border-red-400 focus:border-red-400 focus:ring-red-50",
            !!left && "pl-10",
            !!rest.disabled && "border-gray-500 bg-gray-100",
            className,
          )}
        />
      </div>
      {(!compact || !!message || !!error) && (
        <Message message={message} error={error} />
      )}
    </div>
  ),
);
