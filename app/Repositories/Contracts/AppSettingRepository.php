<?php


namespace App\Repositories\Contracts;


interface AppSettingRepository extends BaseRepository
{
    /**
     * set settings
     *
     * @param array $data
     * @return \ArrayAccess
     */
    public function setSettings(array $data) : \ArrayAccess;

}
