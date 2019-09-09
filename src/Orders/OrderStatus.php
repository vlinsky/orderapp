<?php
namespace App\Orders;

/**
 * OrderStatus
 * 
 */
class OrderStatus
{
    const STATUS_CREATED = 'created';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELED = 'cancelled';
}