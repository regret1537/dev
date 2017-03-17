<?php
interface CardNoCheck
{
    public static function parse($row);

    public static function check($row);
}