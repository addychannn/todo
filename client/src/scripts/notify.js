import { notify } from "notiwind";

const types = {
  default: {
    icon: null,
    classes: {
      wrapper:
        "border border-foreground shadow-foreground/25 bg-background text-foreground",
      iconWrapper: "",
      icon: "",
      title: "drop-shadow-md",
      text: "text-gray-400",
    },
  },
  info: {
    icon: "info",
    classes: {
      wrapper:
        "border border-info shadow-info/25 bg-background text-foreground",
      iconWrapper: "bg-info text-dark",
      icon: "",
      title: "text-info drop-shadow-md",
      text: "text-gray-400",
    },
  },
  warning: {
    icon: "warning",
    classes: {
      wrapper:
        "border border-warning shadow-warning/25 bg-background text-foreground",
      iconWrapper: "bg-warning text-light",
      icon: "",
      title: "text-warning drop-shadow-md",
      text: "text-gray-400",
    },
  },
  positive: {
    icon: "task_alt",
    classes: {
      wrapper:
        "border border-positive shadow-positive/25 bg-background text-foreground",
      iconWrapper: "bg-positive text-light",
      icon: "",
      title: "text-positive drop-shadow-md",
      text: "text-gray-400",
    },
  },
  negative: {
    icon: "error",
    classes: {
      wrapper:
        "border border-negative shadow-negative/25 bg-background text-foreground",
      iconWrapper: "bg-negative text-light",
      icon: "",
      title: "text-negative drop-shadow-md",
      text: "text-gray-400",
    },
  },
};

const notif = (options, duration = 5000) => {
  let _option = Object.assign(
    {
      group: "main",
      icon: options.icon ?? types[options.type]?.icon ?? types.default.icon,
      classes: Object.assign(
        {},
        types[options.type]?.classes ?? types.default.classes,
        options.classes
      ),
      hoverable: true,
      closable: true,
    },
    options
  );

  return notify(_option, duration);
};

export default notif;
