<?php

namespace App\Traits;
use Illuminate\Http\Request;

use App\Models\Permission;

trait PermissionsTrait {
    use LikeToggleTrait;
    public function searchPermissionQuery($search = "") {
        $searchKeys = preg_split("/\s+/", $search ?? "", -1, PREG_SPLIT_NO_EMPTY);
        $permission = Permission::where(function ($query) use ($searchKeys) {
            foreach ($searchKeys as $key) {
                $query->where("name", self::LikeToggle(), "%" . $key . "%");
                // $query->where('description', 'like', '%' . $key . '%');
            }
        });
        return $permission;
    }

    public function searchPermission(
        $search = "",
        $limit = 10,
        $offset = 0,
        $orderBy = "id",
        $order = "asc"
    ) {
        $permission = $this->searchPermissionQuery($search)
            ->orderBy($orderBy, $order)
            ->offset($offset)
            ->limit($limit);
        return $permission;
    }

    public function searchPermissionCount($search = "") {
        $permission = $this->searchPermissionQuery($search)->selectRaw("count(*) as count");
        return $permission;
    }
}
