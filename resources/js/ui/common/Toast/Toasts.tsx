import { ToastMessage } from "./ToastMessage";
import { useToastStore } from "./toastStore";

export const Toasts = () => {
  const { toasts, deleteToast } = useToastStore();

  return !toasts.length ? null : (
    <div
      aria-live="assertive"
      className="pointer-events-none absolute inset-0 flex items-end overflow-hidden px-4 py-6 sm:items-start sm:p-6"
    >
      <div className="flex w-full flex-col items-center space-y-4 sm:items-end">
        {toasts.map((toast) => (
          <ToastMessage
            key={toast.id}
            toast={toast}
            onClose={() => void deleteToast(toast.id)}
          />
        ))}
      </div>
    </div>
  );
};
