import { watch } from "vue";
import { useSystemStore } from "@/stores";

const drawTArt = () => {
  const tart = [
    " █████╗  ██████╗  █████╗ ██████╗ ",
    "██╔══██╗██╔════╝ ██╔══██╗██╔══██╗",
    "██║  ╚═╝██║  ██╗ ██║  ██║██████╦╝",
    "██║  ██╗██║  ╚██╗██║  ██║██╔══██╗",
    "╚█████╔╝╚██████╔╝╚█████╔╝██████╦╝",
    " ╚════╝  ╚═════╝  ╚════╝ ╚═════╝ ",
    "    City Government Of Baguio",
  ];
  const t = `console.log('\\n${tart.join("\\n")}')`;
  eval(t);
};

export default {
  install: (app, options) => {
    drawTArt();

    const systemStore = useSystemStore();
    const setTheme = (isDark) => {
      if (isDark) {
        document.documentElement.classList.add("dark");
      } else {
        document.documentElement.classList.remove("dark");
      }
    };
    setTheme(systemStore.theme.dark);
    watch(() => systemStore.theme.dark, setTheme);
  },
};
