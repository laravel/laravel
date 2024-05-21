import React from "react";
import { Text as ReactEmailText } from "@react-email/components";
import { tv, VariantProps } from "tailwind-variants";

const emailText = tv({
  base: "m-0 leading-tight tracking-[.5%]",
  variants: {
    variant: {
      base: "text-black",
      error: "text-red-500",
      info: "text-blue-500",
      success: "text-green-500",
    },
    size: {
      default: "text-base",
      small: "text-sm",
      title: "text-2xl",
    },
  },
  defaultVariants: {
    variant: "base",
    size: "default",
  },
});

type Props = {
  className?: string;
} & React.PropsWithChildren &
  VariantProps<typeof emailText>;

export const EmailText = ({ variant, size, className, children }: Props) => {
  return (
    <ReactEmailText className={emailText({ variant, size, className })}>
      {children}
    </ReactEmailText>
  );
};
