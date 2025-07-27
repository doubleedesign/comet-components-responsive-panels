<?php
namespace Doubleedesign\Comet\Core;

class Config {
    private static ThemeColor $globalBackground = ThemeColor::WHITE;
    private static string $iconPrefix = 'fa-solid';

    public static function set_global_background(string $color): void {
        self::$globalBackground = ThemeColor::tryFrom($color);
    }

    public static function get_global_background(): string {
        return self::$globalBackground->value;
    }

    public static function set_icon_prefix(string $prefix): void {
        self::$iconPrefix = $prefix;
    }

    public static function get_icon_prefix(): string {
        return self::$iconPrefix;
    }
}
