<?php
namespace App\Test\Orders;

use PHPUnit\Framework\TestCase;
use App\Orders\OrderNotFoundException;
use App\Entity\Orders;
use App\Orders\OrderStatus;
use Doctrine\Common\Persistence\ObjectRepository;
use App\Orders\Order;
use Doctrine\ORM\EntityManager;

class OrderNotFoundExceptionTest extends TestCase
{
    protected $userId;
    protected $order;
    protected $notExistentOrderId = 0;
    
    public function setUp()
    {
       $this->userId = 1; 
       
       $orders = new Orders();
       $orders->setUserId(1);
       $orders->setCreatedDateTime(new \DateTime("now"));
       $orders->setStatus(OrderStatus::STATUS_CREATED);
       
       $orderRepository = $this->createMock(ObjectRepository::class);
       
       $orderRepository->expects($this->any())->method('find')->willReturn($orders);
       
       $entityManager = $this->createMock(EntityManager::class);
       
       $entityManager->expects($this->any())->method('getRepository')->willReturn($orderRepository);
       
       $this->order = new Order(1, $entityManager);
    }
    
    public function testNotFoundException()
    {
        $this->expectException(OrderNotFoundException::class);
        $this->order->setStatus(OrderStatus::STATUS_DELIVERED, $this->notExistentOrderId);
    }
}