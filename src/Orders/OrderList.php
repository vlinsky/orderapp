<?php
namespace App\Orders;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Orders as OrderEntity;

class OrderList
{
    /**
     * EntityManager object
     * @var EntityManagerInterface
     */
    private $em;
    
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    /**
     * Get all orders with specified status
     * 
     * @param string $status
     * @return object[]
     */
    public function getAllByStatus(string $status) : array
    {
        $orders = $this->em->getRepository(OrderEntity::class)->findBy([
            'status' => $status
        ]);
        
        return $orders;
    }
}