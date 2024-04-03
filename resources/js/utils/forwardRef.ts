import React from "react";

// Here we are re-declaring the forwardRef type to support generics being passed to it

// eslint-disable-next-line @typescript-eslint/ban-types
export const forwardRef = React.forwardRef as <T, P = {}>(
  render: (props: P, ref: React.Ref<T>) => React.ReactElement | null,
) => (props: P & React.RefAttributes<T>) => React.ReactElement | null;
