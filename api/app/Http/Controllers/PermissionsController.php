<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Traits\PermissionsTrait;
use App\Traits\RolesTrait;

use App\Models\Role;
use App\Models\Permission;

use App\Http\Resources\RolesResource;
use App\Http\Resources\PermissionsResource;

class PermissionsController extends Controller {
    use PermissionsTrait, RolesTrait;

    public function __construct() {
        $this->middleware("role:Admin", [
            "only" => [
                "createPermission",
                "updatePermission",
                "permissionSearch",
                "permissionDelete",
            ],
        ]);

        $this->middleware("permission:roles_list|roles_add|roles_edit|roles_delete", [
            "only" => ["roleSearch", "rolePermissionSearch"],
        ]);
        $this->middleware("permission:roles_add", ["only" => ["createRole"]]);
        $this->middleware("permission:roles_edit", ["only" => ["updateRole"]]);
        $this->middleware("permission:roles_delete", ["only" => ["deleteRole"]]);
    }

    #region Permissions
    public function createPermission(Request $request) {
        $request->validate([
            "name" => "required|max:75|unique:permissions,name",
            "description" => "max:255",
        ]);

        $permission = Permission::create([
            "name" => $request->name,
            "description" => $request->description,
        ]);

        $this->logInfo('New Permission Created', 'Permissions', $this->ACTION_CREATE, null, $permission);

        return response(
            [
                "message" => "Permission created successfully",
                "data" => new PermissionsResource($permission),
            ],
            201
        );
    }

    public function updatePermission(Request $request, Permission $permission) {
        $request->validate([
            "name" => "required|max:75|unique:permissions,name," . $permission->id . "|not_in:none",
            "description" => "max:255",
        ]);

        $old = $permission->getOriginal();
        $permission->update([
            "name" => $request->input("name"),
            "description" => $request->input("description"),
        ]);

        $this->logInfo('Permission updated', 'Permissions', $this->ACTION_UPDATE, $old, $permission->getChanges());

        return response([
            "message" => "Permission updated successfully",
            "data" => new PermissionsResource($permission),
        ]);
    }

    public function permissionSearch(Request $request) {
        $permissions = $this->searchPermissionRequest($request);

        return response([
            "data" => PermissionsResource::collection($permissions["data"]),
            "count" => $permissions["count"],
        ]);
    }

    public function permissionDelete(Permission $permission) {
        $permission->delete();

        $this->logWarning('Permission deleted', 'Permissions', $this->ACTION_DELETE, $permission, null);

        return response([
            "message" => "Permission deleted successfully"
        ]);
    }
    #endregion

    #region Roles
    public function createRole(Request $request) {
        $request->merge([
            "permissions" => collect($request->permissions)
                ->map(function ($item) {
                    return Permission::hashToId($item);
                })
                ->toArray(),
        ]);
        $request->validate([
            "name" => "required|max:75|unique:roles,name",
            "description" => "max:255",
            "permissions" => "required|array",
            "permissions.*" => "required|integer|exists:permissions,id",
        ]);

        $none = Permission::where("name", "none")->first();
        $permissions = collect($request->permissions);

        if ($permissions->count() > 1 && $permissions->contains($none->id)) {
            $permissions = $permissions->filter(fn($item) => $item != $none->id);
        }

        $role = Role::create([
            "name" => $request->name,
            "description" => $request->description,
        ]);

        $role->syncPermissions($permissions->toArray());

        $result = new RolesResource($role);
        $this->logInfo('Role created', 'Permissions (Roles)', $this->ACTION_CREATE, null, $result);
        return response([
            "message" => "Role created successfully",
            "data" => $result,
        ]);
    }

    public function updateRole(Request $request, Role $role) {
        $request->merge([
            "permissions" => collect($request->permissions)
                ->map(function ($item) {
                    return Permission::hashToId($item);
                })
                ->toArray(),
        ]);
        $request->validate([
            "permissions" => "required|array",
            "permissions.*" => "required|integer|exists:permissions,id",
        ]);

        $old = json_decode(json_encode(new RolesResource(clone $role)));

        if (!$role->protected) {
            $request->validate([
                "name" => "required|max:75|unique:roles,name," . $role->id,
                "description" => "max:255",
                "permissions" => "required|array",
            ]);
            $role->update([
                "name" => $request->input("name"),
                "description" => $request->input("description", null),
            ]);
        }

        $none = Permission::where("name", "none")->first();
        $permissions = collect($request->permissions);

        if ($permissions->count() > 1 && $permissions->contains($none->id)) {
            $permissions = $permissions->filter(fn($item) => $item != $none->id);
        }

        $role->syncPermissions($permissions->toArray());
        $role->with("permissions")->get();

        $result = new RolesResource($role);

        $this->logInfo('Role updated', 'Permissions (Roles)', $this->ACTION_UPDATE, $old, $result);

        return response([
            "message" => "Role updated successfully",
            "data" => $result,
        ]);
    }

    public function roleSearch(Request $request) {
        $search = $request->input("search", "");
        $limit = $request->input("limit", 10);
        $offset = $request->input("offset", 0);
        $orderBy = $request->input("orderBy", "name");
        $order = $request->input("order", "asc");

        $role = $this->searchRole($search, $limit, $offset, $orderBy, $order)
            ->with("permissions:id,name,description")
            ->select(["id", "name", "description", "protected"])
            ->where("name", "<>", "Admin")
            ->get();
        $count = $this->searchRoleCount($search)
            ->where("name", "<>", "Admin")
            ->first()->count;

        return response([
            "data" => RolesResource::collection($role),
            "count" => $count,
        ]);
    }

    public function rolePermissionSearch(Request $request) {
        $permissions = $this->searchPermissionRequest($request);

        return response([
            "data" => PermissionsResource::collection($permissions["data"]),
            "none" => $permissions["none"],
            "count" => $permissions["count"],
        ]);
    }

    public function deleteRole(Role $role) {
        if ($role->protected == 1) {
            return response(
                [
                    "message" => "Cannot delete protected role",
                ],
                403
            );
        }
        $result = collect($role->getOriginal())->put('permissions', PermissionsResource::collection($role->permissions));
        $role->delete();
        $this->logWarning('Role deleted', 'Permissions (Roles)', $this->ACTION_DELETE, $result, null);
        return response([
            "message" => "Role deleted successfully",
        ]);
    }
    #endregion

    private function searchPermissionRequest(Request $request) {
        $search = $request->input("search");
        $limit = $request->input("limit", 10);
        $offset = $request->input("offset", 0);
        $orderBy = $request->input("orderBy", "name");
        $order = $request->input("order", "asc");

        $permissions = $this->searchPermission($search, $limit, $offset, $orderBy, $order)
            ->where("name", "!=", "none")
            ->get();
        $count = $this->searchPermissionCount($search)
            ->where("name", "!=", "none")
            ->first()->count;

        $none = Permission::where("name", "none")->first();

        return [
            "none" => $none->hash,
            "data" => $permissions,
            "count" => $count,
        ];
    }
}
