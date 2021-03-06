<?php

class Session {

    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        self::init();
        $value->token = Hash::whirlpool(time());
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        self::init();
        if (isset($_SESSION[$key]))
            return $_SESSION[$key];
    }

    public static function destroy($key) {
        self::init();
        unset($_SESSION[$key]);
    }

}
