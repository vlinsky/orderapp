<?php
namespace App\Test\Orders;

use PHPUnit\Framework\TestCase;
use App\Entity\Orders;
use App\Orders\OrderStatus;
use Doctrine\Common\Persistence\ObjectRepository;
use App\Orders\Order;
use Doctrine\ORM\EntityManager;

class OrderTest extends TestCase
{
    protected $order;
    
    protected function setUp()
    {
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
    
    public function testCreateOrder()
    {
        $orderEntity = $this->order->create();
        $this->assertInstanceOf(Orders::class, $orderEntity);
        $this->assertEquals('created', $this->order->create()->getStatus());
        
        return $orderEntity;
    }
}