import { Logo } from "@/components";

export const ErrorBoundaryFallback = () => {
  return (
    <main className="flex h-screen flex-col items-center justify-center bg-slate-800">
      <div className="mb-20">
        <Logo />
      </div>
      <h2 className="mb-10 text-2xl">Something went terribly wrong!</h2>
    </main>
  );
};
