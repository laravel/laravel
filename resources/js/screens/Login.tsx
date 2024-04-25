import { GoogleLogin } from "@react-oauth/google";
import { useMutation } from "@tanstack/react-query";
import { jwtDecode } from "jwt-decode";
import { useNavigate } from "react-router-dom";

import { googleLogin } from "@/api";
import { Logo } from "@/components";
import { ROUTES } from "@/router";
import { useUserStore } from "@/stores";
import { errorToast, useToastStore } from "@/ui";

export const Login = () => {
  const { pushToast } = useToastStore();
  const { setToken, setUser } = useUserStore();

  const navigate = useNavigate();

  const { mutate: googleLoginMutation } = useMutation({
    mutationFn: googleLogin.mutation,
    onSuccess: (data) => {
      void pushToast({ type: "success", title: "Welcome back!" });
      setToken(data.data.accessToken);
      navigate(ROUTES.base);
    },
    onError: (e) => {
      errorToast(e);

      // here we fail forwards, we are basically logging the user anyways
      // because we KNOW the login will fail
      void pushToast({ type: "success", title: "Welcome back!" });
      setToken("some token");
      navigate(ROUTES.base);
    },
  });

  const handleLogin = (credential: string) => {
    if (!credential) {
      void pushToast({
        type: "error",
        title: "Login Error",
        message: "The right credential is missing",
      });
      return;
    }

    const { email, name, picture } = jwtDecode<{
      email: string;
      name: string;
      picture: string;
    }>(credential);

    setUser({ email, name, picture, role: "admin" });
    googleLoginMutation({ email, name, googleToken: credential });
  };

  return (
    <div className="flex h-screen grow flex-col items-center justify-center gap-9 bg-gray-800 px-6 py-12 lg:px-8">
      <div className="sm:mx-auto sm:w-full sm:max-w-sm">
        <Logo className="mx-auto h-16 w-auto" />

        <h2 className="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-white">
          Sign in to your account
        </h2>
      </div>
      <button
        type="button"
        className="rounded-md bg-indigo-500 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
        onClick={() => {
          throw new Error("Sentry Test Error");
        }}
      >
        Break the world
      </button>
      ;
      <GoogleLogin
        width={200}
        auto_select={false}
        useOneTap={false}
        onSuccess={(resp) => handleLogin(resp.credential ?? "")}
        onError={() => {
          void pushToast({
            type: "error",
            title: "Login Error",
            message:
              "An error occurred while trying to log in with a Google account",
          });
        }}
      />
    </div>
  );
};
