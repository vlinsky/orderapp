<?php
namespace App\Config;

/**
 * App Config class
 * 
 * get data from .env file
 */
class Config
{
    /**
     * get redis connection string
     * @return string
     */
    public static function getRedisSocket() : string
    {
        return $_ENV['REDIS_CONNECTION_STRING'];
    }
    
    /**
     * get redis session expire time from .env
     * @return string
     */
    public static function getRedisSessionTimeout() : string
    {
        return $_ENV['REDIS_SESSION_TIMEOUT'];
    }
    
    /**
     * get redis session namespace from .env
     * @return string
     */
    public static function getRedisSessionNamespace() : string
    {
        return $_ENV['REDIS_SESSION_NEMASPACE'];
    }
    
    /**
     * get payment service url from .env
     * @return string
     */
    public static function getPaymentUrl()
    {
        return $_ENV['PAYMENT_URL'];
    }
}