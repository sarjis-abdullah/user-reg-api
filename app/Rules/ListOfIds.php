<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ListOfIds implements Rule
{
    /**
     * @var array
     */
    private array $messages;
    /**
     * @var array
     */
    private array $additionalAcceptedValues;

    /**
     * @var null
     */
    private $columnName;
    /**
     * @var null
     */
    private $tableName;

    /**
     * Create a new rule instance.
     *
     * @param null $tableName
     * @param null $columnName
     * @param array $additionalAcceptedValues
     * @param array $messages
     */
    public function __construct($tableName = null, $columnName = null, array $additionalAcceptedValues = [], $messages = [])
    {
        $this->tableName = $tableName;
        $this->columnName = $columnName;
        $this->additionalAcceptedValues = $additionalAcceptedValues;
        $this->messages = $messages;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        //return false ahead if required value is empty
        if (empty($this->tableName) || empty($this->columnName)) {
            $this->messages[] = 'Invalid params in list of ids rule.';
            return false;
        }

        // allowed values csv-string/numeric/array/stringify-json
        if (is_string($value)) {
            //stringify-json
            if (strpos($value,'[') !== false) {
                $ids = json_decode($value, true);

                if (is_null($ids)) {
                    $this->messages[] = 'Invalid params in list of ids rule.';
                    return false;
                }
            } else {
                $ids = explode(',', $value);
            }
        } else if (is_numeric($value)) {
            $ids = [$value];
        } else if (is_array($value)) {
            $ids = $value;
        } else {
            $this->messages[] = 'Invalid list of ids';
            return false;
        }

        $notFoundRows = [];
        foreach ($ids as $id) {

            if (in_array($id, $this->additionalAcceptedValues)) {
                continue;
            }

            if (is_array($id)) {
                return false;
            }
            $table = DB::table($this->tableName);
            $doesntExist = $table->where($this->columnName, $id)->whereNull('deleted_at')->doesntExist();

            if ($doesntExist) {
                $notFoundRows[] = $id;
            }
        }


        if (count($notFoundRows) > 0) {
            $this->messages[] = "Resource with id " . implode(',', $notFoundRows) . " doesn't exist.";
            return false;
        } else {
            if (strpos($attribute, '.') !== false) {
                $keys = explode('.', $attribute);
                $numberOfFields = count($keys);
                $inputs = request()->all();
                switch ($numberOfFields) {
                    case 2:
                        $field[$keys[0]] = $inputs[$keys[0]];
                        $field[$keys[0]][$keys[1]] = array_unique($ids);
                        break;
                    case 3:
                        $field[$keys[0]] = $inputs[$keys[0]];
                        $field[$keys[1]] = $inputs[$keys[1]];
                        $field[$keys[0]][$keys[1][$keys[2]]] = array_unique($ids);
                        break;
                }

            } else {
                $field = [$attribute => array_unique($ids)];
            }
            //$data['some']['thing'] = 'value';
            //dd($data, $field,request()->all());
            // modify the value to array
            request()->merge($field);
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): array|string
    {
        return $this->messages;
    }
}
