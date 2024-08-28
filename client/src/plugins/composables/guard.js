import { useAuthStore } from "../../stores";

const useGuard = () => {
  const authStore = useAuthStore();
  const _super = import.meta.env.VITE_APP_SUPERMAN ?? "Admin";

  const isSuperAdmin = () => hasRole(_super);

  const can = (permission) => {
    if (Array.isArray(permission)) {
      return hasAnyPermission(permission) || isSuperAdmin();
    } else if (typeof permission === "string") {
      return hasPermission(permission) || isSuperAdmin();
    } else return false;
  };

  const hasRole = (role) => {
    return authStore.roles?.includes(role);
  };
  const hasAnyRole = (roles) => {
    return roles.some((role) => hasRole(role));
  };
  const hasAllRoles = (roles) => {
    return roles.every((role) => hasRole(role));
  };

  const hasPermission = (permission) => {
    return authStore.permissions?.includes(permission);
  };

  const hasAnyPermission = (permissions) => {
    if (!Array.isArray(permissions) || permissions.length === 0) {
      return false;
    }
    return permissions.some((permission) => hasPermission(permission));
  };
  const hasAllPermissions = (permissions) => {
    return permissions.every((permission) => hasPermission(permission));
  };

  const hasAll = (permissions, roles) => {
    return hasAllPermissions(permissions) && hasAllRoles(roles);
  };

  const hasOne = (permissions, roles) => {
    return hasAnyPermission(permissions) || hasAnyRole(roles);
  };

  const hasNone = (permissions, roles) => {
    return !hasAnyPermission(permissions) && !hasAnyRole(roles);
  };
  return {
    isSuperAdmin,
    can,
    hasRole,
    hasAnyRole,
    hasAllRoles,
    hasPermission,
    hasAnyPermission,
    hasAllPermissions,
    hasAll,
    hasOne,
    hasNone,
  };
};

export default useGuard;
