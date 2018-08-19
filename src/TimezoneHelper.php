<?php
/**
 * Created by PhpStorm.
 * User: huijiewei
 * Date: 6/4/15
 * Time: 20:55
 */

namespace huijiewei\timezone;

class TimezoneHelper
{
    const WHITESPACE_SEP = '  ';

    public static $continents = [
        'Africa' => \DateTimeZone::AFRICA,
        'America' => \DateTimeZone::AMERICA,
        'Antarctica' => \DateTimeZone::ANTARCTICA,
        'Arctic' => \DateTimeZone::ARCTIC,
        'Asia' => \DateTimeZone::ASIA,
        'Atlantic' => \DateTimeZone::ATLANTIC,
        'Australia' => \DateTimeZone::AUSTRALIA,
        'Europe' => \DateTimeZone::EUROPE,
        'Indian' => \DateTimeZone::INDIAN,
        'Pacific' => \DateTimeZone::PACIFIC,
    ];

    public static function getTimezone()
    {
        $timezones = [];
        $zones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);

        foreach ($zones as $timezone) {
            $timezones[] = $timezone;
        }

        return $timezones;
    }

    public static function getTimezoneLabel($timezone)
    {
        $timezones = static::getTimeZoneArray();

        foreach ($timezones as $mask => $list) {
            foreach ($list as $name => $label) {
                if ($timezone == $name) {
                    return $label;
                }
            }
        }

        return '';
    }

    public static function getTimeZoneArray()
    {
        $timezones = [];

        foreach (self::$continents as $name => $mask) {
            $zones = \DateTimeZone::listIdentifiers($mask);

            foreach ($zones as $timezone) {
                $time = new \DateTime(null, new \DateTimeZone($timezone));

                $timezones[$name][$timezone] = '(GMT ' . $time->format('P') . ')' . self::WHITESPACE_SEP . str_replace('_', ' ', substr($timezone, strlen($name) + 1));
            }
        }

        $timezones['General']['UTC'] = '(UTC)' . self::WHITESPACE_SEP . 'UTC timezone';
        $timezones['General']['GMT'] = '(GMT)' . self::WHITESPACE_SEP . 'GMT timezone';

        return $timezones;
    }

    public static function checkTimezone($timezone)
    {
        $timezones = static::getTimeZoneArray();

        foreach ($timezones as $mask => $list) {
            foreach ($list as $name => $label) {
                if ($timezone == $name) {
                    return true;
                }
            }
        }

        return false;
    }
}
