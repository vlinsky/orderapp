<?php
namespace App\Command;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Orders\OrderList;
use App\Orders\OrderStatus;

/**
 * Order cron class
 * 
 * moves confirmed orders to delivered
 */
class OrderCron extends Command
{
    protected static $defaultName = 'app:order:cron';
    
    private $em;
    private $logger;
    
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
        parent::__construct();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $orderList = new OrderList($this->em);
            $orders = $orderList->getAllByStatus(OrderStatus::STATUS_CONFIRMED);
            
            foreach ($orders as $order) {
                $order->setStatus(OrderStatus::STATUS_DELIVERED, $order->getId());
                
                $this->em->persist($order);
                $this->em->flush();
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            die($e->getMessage());
        }
    }
}