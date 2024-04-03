import type { FieldValues, Path, UseFormSetError } from "react-hook-form";
import { z } from "zod";

const axiosFormErrorSchema = z.object({
  response: z.object({
    data: z.object({
      status: z.number(),
      success: z.boolean(),
      error: z.object({
        code: z.string(),
        message: z.string(),
        fields: z.record(z.array(z.string())),
      }),
    }),
  }),
});

export const validateFormError = (data: unknown) => {
  const parsedError = axiosFormErrorSchema.safeParse(data);

  if (parsedError.success) return parsedError.data;

  return undefined;
};

export const parseAxiosFormErrors = <T extends Record<string, unknown>>(
  apiError?: unknown,
): Partial<Record<keyof T, string[]>> => {
  const parsedError = axiosFormErrorSchema.safeParse(apiError);
  if (parsedError.success) {
    // TODO: find a way to avoid this casting
    return parsedError.data.response.data.error.fields as object;
  }

  return {};
};

export const handleAxiosFieldErrors = <T extends FieldValues>(
  err: unknown,
  setError: UseFormSetError<T>,
) => {
  const formErrors = parseAxiosFormErrors<T>(err);
  const entries = Object.entries(formErrors) as [Path<T>, string[]][];

  entries.forEach(([fieldName, errors]) => {
    const firstError = errors[0];
    if (firstError) {
      setError(
        fieldName,
        { type: "backend", message: firstError },
        { shouldFocus: true },
      );
    }
  });
};
