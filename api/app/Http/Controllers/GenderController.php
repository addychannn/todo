<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\GenderTrait;

use App\Models\Gender;

use App\Http\Resources\GenderResource;

class GenderController extends Controller {
    use GenderTrait;
    public function getGenders() {
        $genders = Gender::select("id", "name", "description AS desc")
            ->where("disabled_at", null)
            ->get();

        return response([
            "data" => $genders->map(function($item, $key) {
                return [
                    'id' => $item->hash,
                    'name' => $item->name,
                    'desc' => $item->desc
                ];
            }),
            "count" => $genders->count()
        ]);
    }

    public function searchGenders(Request $request){
        $search = $request->input("search", "");
        $limit = $request->input("limit", 100);
        $offset = $request->input("offset", 0);
        $orderBy = $request->input("orderBy", "name");
        $order = $request->input("order", "asc");
        $column = $request->input("column", "name");

        $genders = $this->searchGenders($search, $limit, $offset, $orderBy, $order, $column)->get();
        $count = $this->searchGendersCount($search, $column);

        return response([
            "data" => GenderResource::collection($genders),
            "count" => $count
        ]);
    }

    public function addGender(Request $request){
        $request->validate([
            "name" => "required|max:255|unique:genders,name"
        ]);

        $gender = Gender::create([
            "name" => $request->input("name"),
            "description" => $request->input("desc", null),
        ]);

        $this->logInfo("New Gender created: $gender->name", "");

        return new GenderResource($gender);
    }

    public function updateGender(Request $request, Gender $gender){
        $request->validate([
            "name" => "required|max:255|unique:genders,name,".$gender->id.",id",
        ]);

        $gender->update([
            "name" => $request->input("name"),
            "description" => $request->input("desc", null),
        ]);

        return new GenderResource($gender);
    }

    public function toggleGender(Gender $gender){
        $status = !!$gender->disabled_at ? null : now();
        $gender->update(["disabled_at" => $status]);
        return new GenderResource($gender);
    }
}
