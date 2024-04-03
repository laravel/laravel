import type { ComponentPropsWithoutRef, FC, ReactNode } from "react";

import { tw } from "@/utils";

export interface LabelProps extends ComponentPropsWithoutRef<"label"> {
  label: ReactNode;
  containerClassName?: string;
}

export const Label: FC<LabelProps> = ({
  label,
  containerClassName,
  className,
  ...props
}) => (
  <div className={tw("flex", containerClassName)}>
    {typeof label !== "string" ? (
      label
    ) : (
      <label
        {...props}
        className={tw(
          "text-neutrals-dark-900 m-0 mr-3 text-sm font-medium leading-6",
          className,
        )}
      >
        {label}
      </label>
    )}
  </div>
);
