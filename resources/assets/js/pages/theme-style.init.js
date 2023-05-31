function themeColor() {
  var isActiveColor = window.localStorage.getItem("color");
  switch (isActiveColor) {
    case "bgcolor-radio1":
      document.documentElement.style.setProperty(
        "--bs-primary-rgb",
        "78, 172, 109"
      );
      break;

    case "bgcolor-radio2":
      document.documentElement.style.setProperty(
        "--bs-primary-rgb",
        "80, 165, 241"
      );
      break;

    case "bgcolor-radio4":
        document.documentElement.style.setProperty(
            "--bs-primary-rgb",
            "97, 83, 204"
            );
      break;

    case "bgcolor-radio5":
      document.documentElement.style.setProperty(
        "--bs-primary-rgb",
        "232, 62, 140"
      );
      break;

    case "bgcolor-radio6":
        document.documentElement.style.setProperty(
            "--bs-primary-rgb",
            "239, 71, 111"
          );        
      break;

    case "bgcolor-radio7":
    document.documentElement.style.setProperty(
        "--bs-primary-rgb",
        "121, 124, 140"
        ); 
      break;

    default:
        document.documentElement.style.setProperty(
            "--bs-primary-rgb",
            ""
            ); 
  }
}
themeColor();
