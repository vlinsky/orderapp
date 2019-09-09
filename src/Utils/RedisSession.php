<?php
namespace App\Utils;

use App\Config\Config;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use App\Utils\TokenExpiredException;

/**
 * Redis session
 * 
 * store session data in redis
 */
class RedisSession
{
    /**
     * redis client object
     * 
     * can be any redis client that can work in symfony
     * @var RedisAdapter
     */
    private $redisClient;
    
    public function __construct()
    {
        $this->redisClient = RedisAdapter::createConnection(Config::getRedisSocket());
    }
    
    /**
     * set key value
     * 
     * set key value with default expire time
     * 
     * @param string $key
     * @param string $value
     * @return unknown
     */
    public function set(string $key, string $value)
    {
        $key = Config::getRedisSessionNamespace().$key;
        return $this->redisClient->transaction()->set($key, $value)->expire($key, Config::getRedisSessionTimeout())->execute();
    }
    
    /**
     * get key value
     * 
     * get key value and increment expire time
     * 
     * @param string $key
     * @throws TokenExpiredException
     * @return unknown
     */
    public function get(string $key) : string
    {
        $key = Config::getRedisSessionNamespace().$key;
        $res = $this->redisClient->transaction()->get($key)->expire($key, Config::getRedisSessionTimeout())->execute();

        if (empty($res[0])) {
            throw new TokenExpiredException('token expired');
        }
        
        return $res[0];
    }

    /**
     * generate token
     * 
     * generate token using token generator
     *
     * @param TokenGeneratorInterface $tokenGen
     * @return string
     */
    public function generateToken(TokenGeneratorInterface $tokenGen) : string
    {
        return $tokenGen->generateToken();
    }
}