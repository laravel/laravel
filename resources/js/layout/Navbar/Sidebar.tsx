import { Link, useLocation } from "react-router-dom";

import { icons, Logo } from "@/components";
import { ROUTES } from "@/router";
import { useUserStore } from "@/stores";
import { tw } from "@/utils";

const navigation = [
  {
    path: ROUTES.projects,
    label: "Projects",
    icon: <icons.FolderIcon className="w-6" />,
  },
  {
    path: ROUTES.deployments,
    label: "Deployments",
    icon: <icons.ServerIcon className="w-6" />,
  },
  {
    path: ROUTES.activity,
    label: "Activity",
    icon: <icons.SignalIcon className="w-6" />,
  },
  {
    path: ROUTES.domains,
    label: "Domains",
    icon: <icons.GlobeAltIcon className="w-6" />,
  },
  {
    path: ROUTES.usage,
    label: "Usage",
    icon: <icons.ChartBarSquareIcon className="w-6" />,
  },
  {
    path: ROUTES.settings,
    label: "Settings",
    icon: <icons.Cog6ToothIcon className="w-6" />,
  },
] as const;

export const Sidebar = ({
  onCloseSidebar,
}: {
  onCloseSidebar?: () => void;
}) => {
  const { pathname: currentPath } = useLocation();
  const setToken = useUserStore((state) => state.setToken);
  return (
    <div className="flex h-screen grow flex-col gap-y-5 overflow-y-auto bg-black/50 px-6 ring-1 ring-white/5">
      <div className="mx-auto flex h-16 shrink-0 py-6 pr-2">
        <Logo className="h-11" />
      </div>
      <nav className="flex flex-1 flex-col">
        <ul className="flex flex-1 flex-col gap-y-7">
          <li className="flex-1">
            <ul className="relative -mx-2 h-full space-y-1">
              {navigation.map((item) => (
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
                className="h-8 w-8 rounded-full bg-gray-800"
                src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                alt=""
              />
              <span className="sr-only">Your profile</span>
              <span aria-hidden="true">Tom Cook</span>
            </Link>
          </li>
        </ul>
      </nav>
    </div>
  );
};
