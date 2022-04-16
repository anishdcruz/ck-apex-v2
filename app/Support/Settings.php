<?php

namespace App\Support;

use DB;

class Settings {

    /**
     * Get value for the provided key
     *
     * @param  string $key
     * @return value or null
     */
    public function get($key)
    {
        $found = $this->db()
            ->where('key', $key)
            ->first();

        return optional($found)->value;
    }

    public function getMany($keys)
    {
        $output = [];

        foreach($keys as $type => $key) {
            $output[$key] = $this->get($key);
        }

        return $output;
    }

    /**
     * Update the value of the provided key
     *
     * @param string $key
     * @param string $value
     * @return boolean
     */
    public function set($key, $value)
    {
        return $this->db()
            ->where('key', $key)
            ->update(['value' => $value]);
    }

    public function setMany($array)
    {
        foreach($array as $key => $value) {
            $this->db()
                ->where('key', $key)
                ->update(['value' => $value]);
        }
    }

    /**
     * Update the value of key with null
     *
     * @param  string $key
     * @return boolean
     */
    public function forget($key)
    {
        return $this->set($key, null);
    }

    /**
     * Get new database instance
     *
     * @return DB
     */
    protected function db()
    {
        return DB::table('settings');
    }
}
