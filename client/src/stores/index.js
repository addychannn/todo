import { createPinia } from "pinia";
import { createPersistedStatePlugin } from "pinia-plugin-persistedstate-2";
import { useAuthStore } from "@/stores/auth";
import { useSystemStore } from "@/stores/system";
import { useUserStore } from "@/stores/user";
import { usePermissionStore } from "@/stores/permission";
import { useRoleStore } from "@/stores/role";
import { EncryptStorage } from "encrypt-storage";

const pinia = createPinia();

let persistedStateOptions = {};
if (import.meta.env.VITE_STORE_ENCRYPT == "true") {
  const envSalt =
    !!import.meta.env.VITE_STORE_SALT &&
    import.meta.env.VITE_STORE_SALT.length >= 10;
  const salt = envSalt ? import.meta.env.VITE_STORE_SALT : "nosi-ba-lasi";

  const envPrefix = !!import.meta.env.VITE_STORE_PREFIX;
  const prefix = envPrefix
    ? import.meta.env.VITE_STORE_PREFIX
    : "fa0feac2dce13c9e6d13d0baf54e364a";

  const ls = new EncryptStorage(salt, {
    prefix: prefix,
    stateManagementUse: true,
  });
  persistedStateOptions = {
    storage: {
      getItem: (key) => {
        let data = ls.getItem(ls.hash(key))?.replace(/^\s+|\s+$/gm, "") ?? "";
        if (data == "") return "[]";
        return data;
      },
      setItem: (key, value) => ls.setItem(ls.hash(key), value),
      removeItem: (key) => ls.removeItem(ls.hash(key)),
    },
  };
}
pinia.use(createPersistedStatePlugin(persistedStateOptions));

export {
  pinia,
  useAuthStore,
  useSystemStore,
  useUserStore,
  usePermissionStore,
  useRoleStore,
};
