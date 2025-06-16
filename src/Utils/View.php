<?php
namespace App\Utils;
/**
 * This class will be used to manage view sections.
 * It allows starting and ending sections, and retrieving their content.
 */
class View
{
    private static $sections = [];
    private static $sectionStack = [];

    public static function startSection($name)
    {
        self::$sectionStack[] = $name;
        ob_start();
    }

    public static function endSection()
    {
        $name = array_pop(self::$sectionStack);
        self::$sections[$name] = ob_get_clean();
    }

    public static function getSection($name)
    {
        return self::$sections[$name] ?? '';
    }

}
