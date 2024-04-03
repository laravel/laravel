import { Link, useLocation } from "react-router-dom";

import { Logo } from "@/components";
import { ROUTES } from "@/router";
import { useUserStore } from "@/stores";
import { icons } from "@/ui";
import { tw } from "@/utils";

const navigation = [
  {
    path: ROUTES.home,
    label: "Home",
    icon: <icons.HomeIcon className="w-6" />,
    role: ["standard", "admin"],
  },
  {
    path: ROUTES.users,
    label: "Users",
    icon: <icons.UserGroupIcon className="w-6" />,
    role: "admin",
  },
] as const;

export const Sidebar = ({
  onCloseSidebar,
}: {
  onCloseSidebar?: () => void;
}) => {
  const { pathname: currentPath } = useLocation();
  const { user, setToken } = useUserStore();
  return (
    <div className="flex h-screen grow flex-col gap-y-12 overflow-y-auto bg-black/50 px-6 ring-1 ring-white/5">
      <div className="mx-auto flex h-16 shrink-0 py-6 pr-2">
        <Logo className="h-11" />
      </div>
      {user && (
        <nav className="flex flex-1 flex-col">
          <ul className="flex flex-1 flex-col gap-y-7">
            <li className="flex-1">
              <ul className="relative -mx-2 h-full space-y-1">
                {navigation
                  .filter((item) => item.role.includes(user.role))
                  .map((item) => (
                    <li key={item.label}>
                      <Link
                        to={item.path}
                        onClick={onCloseSidebar}
                        className={tw(
                          item.path == currentPath
                            ? "bg-gray-800 text-white"
                            : "text-gray-400 hover:bg-gray-800 hover:text-white",
                          "group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6",
                        )}
                      >
                        {item.icon}
                        {item.label}
                      </Link>
                    </li>
                  ))}
                <li className="absolute bottom-0 w-full">
                  <button
                    onClick={() => setToken(null)}
                    className="group flex w-full gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-400 hover:bg-gray-800 hover:text-white"
                  >
                    <icons.ArrowLeftOnRectangleIcon className="w-6" />
                    Sign Out
                  </button>
                </li>
              </ul>
            </li>

            <li className="-mx-6 mt-auto">
              <Link
                to="#"
                className="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-white hover:bg-gray-800"
              >
                <img
                  referrerPolicy="no-referrer"
                  className="h-8 w-8 rounded-full bg-gray-800"
                  src={user.picture}
                  alt={user.name}
                />

                <span className="sr-only">Your profile</span>

                <span aria-hidden="true">{user.name}</span>
              </Link>
            </li>
          </ul>
        </nav>
      )}
    </div>
  );
};
