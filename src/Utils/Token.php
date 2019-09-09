<?php
namespace App\Utils;

use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;

/**
 * Token generator
 * 
 * extend UriSafeTokenGenerator
 */
class Token extends UriSafeTokenGenerator
{
    
}