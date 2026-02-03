<?php

declare(strict_types=1);

namespace WebtronicIE\GiftCards\Models;

use Igniter\Flame\Database\Model;
use Igniter\System\Actions\SettingsModel;
use IgniterLabs\GiftUp\Classes\Manager;

/**
 * Settings Model for GiftUp integration.
 *
 * @method static mixed get(string $key, mixed $default = null)
 * @method static mixed set(string|array $key, mixed $value = null)
 * @mixin SettingsModel
 */
class Settings extends Model
{
    public array $implement = [SettingsModel::class];

    // A unique code
    public string $settingsCode = 'webtronicie_gifcards_settings';

    // Reference to field configuration
    public string $settingsFieldsConfig = 'settings';



    public static function getApiKey(): string
    {
        return (string)self::get('api_key');
    }


    public static function getApiEndPoint(): string
    {
        return (string)self::get('api_endpoint');
    }



    public static function getMinimumValue(): int
    {
        return (int)self::get('minimum_value', 0); // @phpstan-ignore-line arguments.count
    }




}
