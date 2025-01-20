<?php

namespace App\Model;

use Config\Database;
use PDO;
use PDOException;

class Validation {


    public static function validateEmail($email) {

        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }


    public static function validatePassword($password) {

        return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password);
    }


    public static function validateLink($url) {

        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function validateTextarea($textarea) {
        return !empty($textarea) && strlen($textarea) <= 1000;
    }


    public static function validateText($text) {

        return !empty($text);
    }


    public static function validate($input, $type) {
        switch ($type) {
            case 'email':

                return self::validateEmail($input);

            case 'password':

                return self::validatePassword($input);

            case 'link':
                
                return self::validateLink($input);
            case 'textarea':
                
                return self::validateTextarea($input);

            case 'text':
                return self::validateText($input);
                
            default:
                return false;
        }
    }
}
