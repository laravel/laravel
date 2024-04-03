export const asyncTimeout = (ms: number) =>
  new Promise<void>((resolve) => setTimeout(resolve, ms));
