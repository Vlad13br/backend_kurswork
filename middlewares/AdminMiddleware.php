<?php

class AdminMiddleware
{
    public static function handle()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
            header("Location: /");
            exit();
        }
        return true;
    }
}
