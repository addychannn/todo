<?php

namespace App\Traits;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Profile;

trait Helpers
{
    use LikeToggleTrait;
    protected function createSlug($title, $modelName, $id = 0)
    {
        $slug = Str::slug($title);
        $allSlugs = $this->getRelatedSlugs($slug, $modelName, $id);
        if (! $allSlugs->contains('slug', $slug)){
            return $slug;
        }

        $i = 1;
        $is_contain = true;
        do {
            $newSlug = $slug . '-' . $i;
            if (!$allSlugs->contains('slug', $newSlug)) {
                $is_contain = false;
                return $newSlug;
            }
            $i++;
        } while ($is_contain);
    }

    private function getRelatedSlugs($slug, $modelName, $id = 0)
    {
        $model = app("App\\Models\\$modelName");
        return $model->select('slug')->where('slug', self::LikeToggle(), $slug.'%')
        ->where('id', '<>', $id)
        ->get();
    }

    protected function profileFullName(Profile $profile){
        return $this->fullName($profile->first_name, $profile->middle_name, $profile->last_name, $profile->sufix);
    }

    protected function fullName($first_name = null, $middle_name = null, $last_name = null, $suffix = null){
        $fullName = null;
        if($first_name){
            $fullName .= $first_name;
        }
        if($middle_name){
            $fullName .= ' '.substr($middle_name, 0, 1).'.';
        }
        if($last_name){
            $fullName .= ' '.$last_name;
        }
        if($suffix){
            $fullName .= ' '.$suffix;
        }

        return $fullName ? ucwords(strtolower($fullName)) : null;
    }

    protected function filenameValidator ($fname, $replacement = " ") {
        $tmp = collect(preg_split("/\r\n|\n|\r/", $fname));
        $tmp = $tmp->map(function ($item, $key) {
            return trim($item);
        });
        $tmp = array_filter($tmp->toArray());

        $result = join(" ", $tmp);

        $result = preg_replace("/[\r\n]/", " ", $fname);

        $result = preg_replace('/[<>:"\/\\\|?*\x00-\x1F]/', $replacement, $result);

        $result = preg_replace('/\.$/', $replacement, $result);

        if(strlen($result) <= 0){
            $result = "_".$result;
        }

        if(preg_match('/^(CON|PRN|AUX|NUL|COM1|COM2|COM3|COM4|COM5|COM6|COM7|COM8|COM9|LPT1|LPT2|LPT3|LPT4|LPT5|LPT6|LPT7|LPT8|LPT9)(\..+)?$/', $result)){
            $result = "_".$result;
        }

        return $result;
    }

    protected function truthy($value){
        return in_array($value, array(true, 1, "1", "true", "on"));
    }

    protected function falsy($value){
        return in_array($value, array(false, 0, "0", "false", "off"));
    }
}