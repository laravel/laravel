import {
  CheckCircleIcon,
  ExclamationCircleIcon,
  InformationCircleIcon,
  XCircleIcon,
} from "@heroicons/react/24/outline";

const toastIcons = {
  info: <InformationCircleIcon className="h-6 w-6 text-blue-400" />,
  success: <CheckCircleIcon className="h-6 w-6 text-green-400" />,
  warning: <ExclamationCircleIcon className="h-6 w-6 text-yellow-400" />,
  error: <XCircleIcon className="h-6 w-6 text-red-400" />,
};

export default toastIcons;
