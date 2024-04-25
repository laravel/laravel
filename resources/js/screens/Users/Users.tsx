import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";

import { deleteUser, getUsersQuery } from "@/api";
import { MODAL_ROUTES } from "@/router";
import { useNavigateModal } from "@/router/useNavigateModal";
import { Button, errorToast, icons, useToastStore } from "@/ui";
import { tw } from "@/utils";

const statuses = {
  Completed: "text-green-400 bg-green-400/10",
  Error: "text-rose-400 bg-rose-400/10",
};
const activityItems = [
  {
    user: {
      name: "Michael Foster",
      imageUrl:
        "https://images.unsplash.com/photo-1519244703995-f4e0f30006d5?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
    },
    commit: "2d89f0c8",
    branch: "main",
    status: "Completed",
    duration: "25s",
    date: "45 minutes ago",
    dateTime: "2023-01-23T11:00",
  },
  {
    user: {
      name: "Lindsay Walton",
      imageUrl:
        "https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
    },
    commit: "249df660",
    branch: "main",
    status: "Completed",
    duration: "1m 32s",
    date: "3 hours ago",
    dateTime: "2023-01-23T09:00",
  },
  {
    user: {
      name: "Courtney Henry",
      imageUrl:
        "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
    },
    commit: "11464223",
    branch: "main",
    status: "Error",
    duration: "1m 4s",
    date: "12 hours ago",
    dateTime: "2023-01-23T00:00",
  },
  {
    user: {
      name: "Courtney Henry",
      imageUrl:
        "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
    },
    commit: "dad28e95",
    branch: "main",
    status: "Completed",
    duration: "2m 15s",
    date: "2 days ago",
    dateTime: "2023-01-21T13:00",
  },
  {
    user: {
      name: "Michael Foster",
      imageUrl:
        "https://images.unsplash.com/photo-1519244703995-f4e0f30006d5?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
    },
    commit: "624bc94c",
    branch: "main",
    status: "Completed",
    duration: "1m 12s",
    date: "5 days ago",
    dateTime: "2023-01-18T12:34",
  },
  {
    user: {
      name: "Courtney Henry",
      imageUrl:
        "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
    },
    commit: "e111f80e",
    branch: "main",
    status: "Completed",
    duration: "1m 56s",
    date: "1 week ago",
    dateTime: "2023-01-16T15:54",
  },
  {
    user: {
      name: "Michael Foster",
      imageUrl:
        "https://images.unsplash.com/photo-1519244703995-f4e0f30006d5?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
    },
    commit: "5e136005",
    branch: "main",
    status: "Completed",
    duration: "3m 45s",
    date: "1 week ago",
    dateTime: "2023-01-16T11:31",
  },
  {
    user: {
      name: "Whitney Francis",
      imageUrl:
        "https://images.unsplash.com/photo-1517365830460-955ce3ccd263?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
    },
    commit: "5c1fd07f",
    branch: "main",
    status: "Completed",
    duration: "37s",
    date: "2 weeks ago",
    dateTime: "2023-01-09T08:45",
  },
] as const;

export const Users = () => {
  const { pushToast } = useToastStore();
  const queryClient = useQueryClient();

  const { data: users, isLoading: isLoadingUsers } = useQuery({
    ...getUsersQuery(),
    select: (users) =>
      users.map((user, idx) => {
        const selectedItem =
          activityItems[idx % activityItems.length] ?? activityItems[0];

        return {
          ...selectedItem,

          user: {
            imageUrl: selectedItem.user.imageUrl,
            name: user.name,
            id: user.id,
          },
        };
      }),
  });

  const { mutate: deleteUserMutation } = useMutation({
    mutationFn: deleteUser.mutation,
    onSuccess: (_, requestedId) => {
      deleteUser.invalidates(queryClient, { userId: requestedId });
      void pushToast({
        type: "success",
        title: "Success",
        message: "User successfully deleted!",
      });
    },
    onError: errorToast,
  });

  const navigateModal = useNavigateModal();

  return (
    <div className="bg-gray-900">
      <h2 className="flex items-center justify-between px-4 py-9 text-base font-semibold leading-7 text-white sm:px-6 lg:px-8">
        Latest activity
        <Button
          variant="secondary"
          onClick={() => navigateModal(MODAL_ROUTES.userForm)}
        >
          Create user
        </Button>
      </h2>

      <table className="w-full whitespace-nowrap text-left">
        <colgroup>
          <col className="w-full sm:w-4/12" />

          <col className="lg:w-4/12" />

          <col className="lg:w-2/12" />

          <col className="lg:w-1/12" />

          <col className="lg:w-1/12" />

          <col className="lg:w-1/12" />
        </colgroup>

        <thead className="border-b border-white/10 text-sm leading-6 text-white">
          <tr>
            <th
              scope="col"
              className="py-2 pl-4 pr-8 font-semibold sm:pl-6 lg:pl-8"
            >
              User
            </th>

            <th
              scope="col"
              className="hidden py-2 pl-0 pr-8 font-semibold sm:table-cell"
            >
              Commit
            </th>

            <th
              scope="col"
              className="py-2 pl-0 pr-4 text-right font-semibold sm:pr-8 sm:text-left lg:pr-20"
            >
              Status
            </th>

            <th
              scope="col"
              className="hidden py-2 pl-0 pr-8 font-semibold md:table-cell lg:pr-20"
            >
              Duration
            </th>

            <th
              scope="col"
              className="hidden py-2 pl-0 pr-4 text-right font-semibold sm:table-cell sm:pr-6 lg:pr-8"
            >
              Deployed at
            </th>

            <th
              scope="col"
              className="hidden py-2 pl-0 pr-4 text-right font-semibold sm:table-cell sm:pr-6 lg:pr-8"
            >
              <span className="sr-only">Action</span>
            </th>
          </tr>
        </thead>

        <tbody className="divide-y divide-white/5">
          {isLoadingUsers && (
            <tr className="h-full items-center">
              <td colSpan={5}>
                <div className="flex justify-center p-9">
                  <icons.SpinnerIcon />
                </div>
              </td>
            </tr>
          )}
          {users?.map((item) => (
            <tr key={item.commit}>
              <td className="py-4 pl-4 pr-8 sm:pl-6 lg:pl-8">
                <div className="flex items-center gap-x-4">
                  <img
                    src={item.user.imageUrl}
                    alt=""
                    className="h-8 w-8 rounded-full bg-gray-800"
                  />
                  <div className="truncate text-sm font-medium leading-6 text-white">
                    {item.user.name}
                  </div>
                </div>
              </td>
              <td className="hidden py-4 pl-0 pr-4 sm:table-cell sm:pr-8">
                <div className="flex gap-x-3">
                  <div className="font-mono text-sm leading-6 text-gray-400">
                    {item.commit}
                  </div>

                  <div className="rounded-md bg-gray-700/40 px-2 py-1 text-xs font-medium text-gray-400 ring-1 ring-inset ring-white/10">
                    {item.branch}
                  </div>
                </div>
              </td>
              <td className="py-4 pl-0 pr-4 text-sm leading-6 sm:pr-8 lg:pr-20">
                <div className="flex items-center justify-end gap-x-2 sm:justify-start">
                  <time
                    className="text-gray-400 sm:hidden"
                    dateTime={item.dateTime}
                  >
                    {item.date}
                  </time>

                  <div
                    className={tw(
                      statuses[item.status],
                      "flex-none rounded-full p-1",
                    )}
                  >
                    <div className="h-1.5 w-1.5 rounded-full bg-current" />
                  </div>
                  <div className="hidden text-white sm:block">
                    {item.status}
                  </div>
                </div>
              </td>
              <td className="hidden py-4 pl-0 pr-8 text-sm leading-6 text-gray-400 md:table-cell lg:pr-20">
                {item.duration}
              </td>

              <td className="hidden py-4 pl-0 pr-4 text-right text-sm leading-6 text-gray-400 sm:table-cell sm:pr-6 lg:pr-8">
                <time dateTime={item.dateTime}>{item.date}</time>
              </td>

              <td className="hidden py-4 pl-0 pr-4 text-right text-sm leading-6 text-gray-400 sm:table-cell sm:pr-6 lg:pr-8">
                <Button
                  variant="tertiary"
                  onClick={() => deleteUserMutation(item.user.id)}
                >
                  <icons.TrashIcon className="h-5 w-5" />
                </Button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};
