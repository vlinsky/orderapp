<?php
namespace App\Orders;

use App\Entity\Orders;

/**
 * OrderInterface
 */
interface OrderInterface
{   
    /**
     * create order
     * 
     * create new order and return Orders entity
     * 
     * @return Order entity
     */
    public function create() : Orders;
    
    /**
     * get order status
     * 
     * @param mixed $orderId
     * @return string
     */
    public function getStatus($orderId) : string;
    
    /**
     * set order status
     * 
     * @param mixed $status
     * @param mixed $orderId
     * @return bool
     */
    public function setStatus($status, $orderId) : bool;
}