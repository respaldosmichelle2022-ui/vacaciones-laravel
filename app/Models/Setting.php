<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value'
    ];

    public static function getVal($key, $default = null)
    {
        $setting = self::find($key);
        return $setting ? $setting->value : $default;
    }

    public static function setVal($key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
