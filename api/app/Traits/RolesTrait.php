<?php

namespace App\Traits;
use Illuminate\Http\Request;
use App\Models\Role;

trait RolesTrait {
    use LikeToggleTrait;
    public function searchRoleQuery($search) {
        $searchKeys = preg_split("/\s+/", $search ?? "", -1, PREG_SPLIT_NO_EMPTY);
        $role = Role::where(function ($query) use ($searchKeys) {
            foreach ($searchKeys as $key) {
                $query->orWhere("name", self::LikeToggle(), "%" . $key . "%");
            }
        });
        return $role;
    }

    public function searchRole(
        $search,
        $limit = 10,
        $offset = 0,
        $orderBy = "name",
        $order = "asc"
    ) {
        $role = $this->searchRoleQuery($search)
            ->orderBy("protected", "DESC")
            ->orderBy($orderBy, $order)
            ->offset($offset)
            ->limit($limit);
        return $role;
    }

    public function searchRoleCount($search) {
        $role = $this->searchRoleQuery($search)->selectRaw("count(*) as count");
        return $role;
    }
}
