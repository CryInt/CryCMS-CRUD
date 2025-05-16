<?php
namespace CryCMS;

use JetBrains\PhpStorm\NoReturn;

class CRUDHelper
{
    public static function clean($string, $max_length = false): string
    {
        $string = strip_tags($string);
        $string = htmlentities($string, ENT_NOQUOTES, "UTF-8");

        if (!empty($max_length)) {
            $string = mb_substr($string, 0, $max_length, 'UTF-8');
        }

        return trim($string);
    }

    public static function transliteration($text): string
    {
        $text = trim($text);

        $text = mb_strtolower($text, "utf-8");

        $from = array( ' ', '.', ',', '+',    '/', '*', 'а', 'б', 'в', 'г', 'д', 'е', 'ё' , 'ж' , 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х',  'ц',  'ч',  'ш',  'щ',    'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $to   = array( '-', '-', '-', 'plus', '-', '-', 'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'ts', 'ch', 'sh', 'shch', '' , 'y', '' , 'e', 'yu', 'ya');

        $text = str_replace( $from, $to, $text );

        $from = array( '!', '(', ')', '«', '»', '"', "'", '&', '%', ':', '#', '@', '?', '№', '\\', '[', ']' );

        $text = str_replace( $from, '', $text);
        $text = trim($text, " .-,_");

        return preg_replace('/-{2,}/', '-', $text);
    }

    #[NoReturn]
    public static function redirect($location): void
    {
        Header("Location: " . $location);
        exit;
    }
}