import { createBrowserRouter } from "react-router-dom";

import Home from "./routes/Home";

export default createBrowserRouter([
  {
    element: <Home />,
    path: "/",
  },
]);
