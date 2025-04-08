<?php

class RateLimitMiddleware
{
    private static $rateLimit = 30;
    private static $timeWindow = 60;

    public static function handle()
    {
        if (isset($_SESSION['user_id'])) {
            self::applyRateLimit('auth');
        } else {
            self::applyRateLimit('guest');
        }
    }

    private static function applyRateLimit($type)
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        $key = "rate_limit_{$type}_{$ip}";

        $requests = $_SESSION[$key] ?? [];

        $requests = array_filter($requests, function($timestamp) {
            return $timestamp > (time() - self::$timeWindow);
        });

        if (count($requests) >= self::$rateLimit) {
            http_response_code(429);
            echo "Too Many Requests. Please try again later after 1 minute.";
            exit();
        }

        $requests[] = time();

        $_SESSION[$key] = $requests;
    }
}
