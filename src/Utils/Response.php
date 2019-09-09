<?php
namespace App\Utils;

use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

/**
 * Response
 * 
 * Http response in json format
 */
class Response extends HttpFoundationResponse
{
    private $defaultHeades = [
        'Content-Type' => 'application/json',
        'Access-Control-Allow-Origin' => '*',
    ];
    
    public function __construct($content = '', int $status = 200, array $headers = array())
    {
        $mHeaders = array_merge($headers, $this->defaultHeades);
        parent::__construct(json_encode($content), $status, $mHeaders);
    }
}