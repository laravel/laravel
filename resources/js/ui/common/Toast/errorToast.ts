import { z } from "zod";

import { useToastStore } from "./toastStore";

const axiosErrorSchema = z.object({
  response: z.object({
    data: z.object({
      status: z.number(),
      success: z.boolean(),
      error: z.object({
        code: z.string(),
        message: z.string(),
      }),
    }),
  }),
});

export const validateError = (data: unknown) => {
  const parsedError = axiosErrorSchema.safeParse(data);

  if (parsedError.success) return parsedError.data;

  return undefined;
};

export const errorToast = (error: unknown): void => {
  const pushToast = useToastStore.getState().pushToast;

  const validatedError = validateError(error);

  if (validatedError) {
    void pushToast({
      type: "error",
      title: "Validation Error",
      message: validatedError.response.data.error.message,
    });
  } else {
    console.error(error);
    void pushToast({
      type: "error",
      title: "Error",
      message: "Unknown error",
    });
  }
};
