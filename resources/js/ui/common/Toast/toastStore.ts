import type { ReactNode } from "react";
import { v4 as uuid } from "uuid";
import { create } from "zustand";

import { asyncTimeout } from "@/utils/asyncTimeout";

export const toastTypes = ["info", "success", "error", "warning"] as const;

export type ToastType = (typeof toastTypes)[number];

export interface Toast {
  id: string;
  type: ToastType;
  icon: ReactNode;
  title: string;
  message: string;
  timestamp: number; // date.now()
  duration: number; // in ms
  state: "open" | "isClosing";
}

export interface ToastStore {
  toasts: Toast[];
  pushToast: (newToast: Partial<Toast>) => Promise<void>;
  deleteToast: (id: string) => Promise<void>;
}

export const useToastStore = create<ToastStore>((set, get) => ({
  toasts: [],
  pushToast: async (toast = {}) => {
    const newToast = {
      id: uuid(),
      type: "info",
      timestamp: Date.now(),
      icon: null,
      duration: 5000,
      state: "open",
      ...toast,
    } as Toast;

    set((state) => ({
      toasts: state.toasts.concat(newToast),
    }));
    // let's wait for duration and THEN delete this toast if it exists
    await asyncTimeout(newToast.duration);

    void get().deleteToast(newToast.id);
  },
  deleteToast: async (id) => {
    set((state) => {
      const toastIdx = state.toasts.findIndex((t) => t.id === id);
      const toast = state.toasts[toastIdx];

      if (toast) {
        return {
          toasts: state.toasts
            .slice(0, toastIdx)
            .concat({ ...toast, state: "isClosing" })
            .concat(state.toasts.slice(toastIdx + 1)),
        };
      }

      return state;
    });

    await asyncTimeout(500);

    set((state) => ({
      toasts: state.toasts.filter((toast) => toast.id !== id),
    }));
  },
}));
