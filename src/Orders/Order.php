<?php
namespace App\Orders;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Orders as OrderEntity;

class Order implements OrderInterface
{
    /**
     * user id
     * 
     * @var int
     */
    private $userId;
    
    /**
     * EntityManagerInterface
     * 
     * @var EntityManagerInterface
     */
    private $em;
    
    /**
     * 
     * @param int $userId
     * @param EntityManagerInterface $em
     */
    public function __construct(int $userId, EntityManagerInterface $em)
    {
        $this->userId = $userId;
        $this->em = $em;
    }
    
    /**
     * Set userId 
     * 
     * @param int $userId
     */
    public function setUserId(int $userId) : void
    {
        $this->userId = $userId;
    }
    
    /**
     * Get userId
     * 
     * @return int
     */
    public function getUserId() : int
    {
        return $this->userId;
    }
    
    /**
     * set EntityManager
     * 
     * @param EntityManagerInterface $em
     */
    public function setEntityManager(EntityManagerInterface $em) : void
    {
        $this->em = $em;
    }
    
    /**
     * get EntityManager
     * 
     * @return EntityManagerInterface
     */
    public function getEntityManager() : EntityManagerInterface
    {
        return $this->em;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Orders\OrderInterface::create()
     */
    public function create(): OrderEntity
    {
        try {
            $order = new OrderEntity();
            
            $order->setUserId($this->userId);
            $order->setCreatedDateTime(new \DateTime("now"));
            $order->setStatus(OrderStatus::STATUS_CREATED);
            
            $this->em->persist($order);
            $this->em->flush();
            
            return $order;
        } catch (\Exception $e) {
            throw new OrderBaseException($e->getMessage());
        }
        
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Orders\OrderInterface::getStatus()
     */
    public function getStatus($orderId): string
    {
        $order = $this->em->getRepository(OrderEntity::class)->findOneBy([
            'userId' => $this->userId,
            'id' => $orderId
        ]);
        
        if (!$order) {
            throw new OrderNotFoundException('order with id '.$orderId.' not found.');
        }
            
        return $order->getStatus();
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \App\Orders\OrderInterface::setStatus()
     */
    public function setStatus($status, $orderId): bool
    {
          $order = $this->em->getRepository(OrderEntity::class)->findOneBy([
              'userId' => $this->userId,
              'id' => $orderId
          ]);
            
          if (!$order) {
              throw new OrderNotFoundException('order with id '.$orderId.' not found.');
          }
            
          $order->setStatus($status, $orderId);
          
          $this->em->persist($order);
          $this->em->flush();
          
          return true;
    }
}