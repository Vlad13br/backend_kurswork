<?php

class AuthMiddleware
{
    public static function handle()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }
        return true;
    }
}
