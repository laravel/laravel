import { z, ZodError } from "zod";

import { useToastStore } from "./ui";

// We've defined here the validations & schema for making sure the env vars ARE correct.
const defaultValidation = z.string().min(1, "Env Var is not defined");
const envSchema = z.object({
  VITE_APP_ENV: defaultValidation,
  VITE_APP_NAME: defaultValidation,
  VITE_API_URL: defaultValidation,
  VITE_GOOGLE_AUTH_SSO_CLIENT_ID: defaultValidation,
  VITE_SENTRY_DSN_PUBLIC: defaultValidation,
  VITE_SENTRY_TRACE_PROPAGATION_TARGET_REGEX: defaultValidation,
});

type EnvValues = z.infer<typeof envSchema>;

// IDEA: trigger a toast message on dev instead of just a console error
function logEnvError(errors: ZodError<EnvValues>) {
  const { _errors, ...formattedErrors } = errors.format();

  if (import.meta.env.VITE_APP_ENV === "local") {
    const pushToast = useToastStore.getState().pushToast;
    void pushToast({
      type: "error",
      title: "ENVIRONMENT VARIABLES ERRORS",
      message: `${Object.entries(formattedErrors)
        .map(([name, { _errors }]) => `"${name}": ${_errors.join(", ")}`)
        .join("\n")}`,
    });
  } else {
    console.error(
      `\nENVIRONMENT VARIABLES ERRORS:\n-----\n${Object.entries(formattedErrors)
        .map(([name, { _errors }]) => `"${name}": ${_errors.join(", ")}`)
        .join("\n")}\n-----\n`,
    );
  }
}

function checkEnv() {
  try {
    return envSchema.parse(import.meta.env);
  } catch (errors) {
    if (errors instanceof ZodError) {
      logEnvError(errors as ZodError<EnvValues>);
    }

    // So! We had some validation errors
    // let's just make sure the shape of the object is what we are expecting
    return Object.fromEntries(
      Object.keys(envSchema.shape).map((key) => [
        key,
        import.meta.env[key] || "",
      ]),
    ) as EnvValues;
  }
}

export const env = checkEnv();
