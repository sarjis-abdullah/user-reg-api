<?php

namespace App\Http\Requests\Reports;

use App\Http\Requests\Request;

class ModelInstanceCountRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "modelNames" => "array|min:1",
            "modelNames.*" => "string|distinct|in:". implode(',', $this->getModels()),
            'branchId' => 'numeric',
            "dataOf" =>"required|in:today,this_week,this_month,last_month,this_year,all"
        ];
    }

    protected function getModels() {
        $path = app_path() . "/Models";

        $out = [];
        $results = scandir($path);
        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;
            $filename = $result;
            if (is_dir($filename)) {
                $out = array_merge($out, $this->getModels($filename));
            }else{
                $out[] = substr($filename,0,-4);
            }
        }

        return $out;
    }
}
