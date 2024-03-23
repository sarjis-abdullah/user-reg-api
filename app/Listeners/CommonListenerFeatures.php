<?php

namespace App\Listeners;


trait CommonListenerFeatures
{
    /**
     * get changed data between models
     *
     * @param $newModel
     * @param $oldModel
     * @return array
     */
    public function getChangedData($newModel, $oldModel)
    {
        // FIY, only single dimensional array
        $newData = array_filter($newModel->toArray(), function ($element) {
            return !is_array($element);
        });

        $oldData = array_filter($oldModel->toArray(), function ($element) {
            return !is_array($element);
        });

        $changedData = array_diff_assoc($newData, $oldData);
        unset($changedData['updated_at']);
        return $changedData;
    }

    /**
     * has a field value changed
     *
     * @param \ArrayAccess $newModel
     * @param \ArrayAccess $oldModel
     * @param string $fieldName
     * @return array|bool
     */
    public function hasAFieldValueChanged($newModel, $oldModel, $fieldName)
    {
        $changedData = $this->getChangedData($newModel, $oldModel);
        return array_key_exists($fieldName, $changedData) ? $changedData : false;
    }

    /**
     * has field value changed to a specific value
     *
     * @param \ArrayAccess $newModel
     * @param \ArrayAccess $oldModel
     * @param string $fieldName
     * @param mixed $value
     * @return bool
     */
    public function hasAFieldValueChangedTo($newModel, $oldModel, $fieldName, $value)
    {
        $changedData = $this->hasAFieldValueChanged($newModel, $oldModel, $fieldName);
        if ($changedData) {
            return $changedData[$fieldName] === $value ? true : false;
        }

        return false;
    }

    /**
     * @param $eventOptions
     * @return void
     */
    public function mergeRequestsForFutureEvents($eventOptions)
    {
        request()->merge($eventOptions['request']);
    }
}
