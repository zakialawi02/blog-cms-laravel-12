<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class WebSetting extends Model
{
    use HasFactory;

    protected $table = 'web_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    /**
     * Get the value attribute.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        switch ($this->type) {
            case 'integer':
                return (int) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
            case 'array':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Set the value attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setValueAttribute($value): void
    {
        if (in_array($this->type, ['json', 'array']) && is_array($value)) {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Get all settings as an associative array and cache them.
     *
     * @param bool $forceRefresh Whether to force refresh the cache.
     * @return array
     */
    public static function getAllSettings(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            Cache::forget('web_setting');
        }

        return Cache::rememberForever('web_setting', function () {
            return self::all()->pluck('value', 'key')->all();
        });
    }

    /**
     * Get a specific setting by its key.
     *
     * @param string $key The key of the setting.
     * @param mixed $default The default value if the setting is not found.
     * @return mixed
     */
    public static function getSetting(string $key, mixed $default = null): mixed
    {
        $settings = self::getAllSettings();
        return $settings[$key] ?? $default;
    }

    /**
     * Update or create a setting and refresh the cache.
     *
     * @param string $key The key of the setting.
     * @param mixed $value The value of the setting.
     * @param string $type The type of the setting.
     * @return Setting
     */
    public static function setSetting(string $key, mixed $value, string $type = 'string'): WebSetting
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );

        // Refresh the cache
        self::getAllSettings(true);

        return $setting;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        // Clear the cache when a setting is saved or deleted.
        static::saved(function () {
            self::getAllSettings(true); // Force refresh cache
        });

        static::deleted(function () {
            self::getAllSettings(true); // Force refresh cache
        });
    }
}
