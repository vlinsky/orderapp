<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\RedisSession;
use App\Utils\TokenExpiredException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use App\Orders\Order;
use App\Utils\Response;
use App\Orders\OrderBaseException;
use App\Orders\OrderNotFoundException;
use App\Orders\OrderStatus;

/**
 * OrderController
 * 
 * Order REST API
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/order/create/", name="ordercreate", methods={"POST"})
     */
    public function create(RedisSession $rsession, Request $request, EntityManagerInterface $em, LoggerInterface $logger)
    {   
        try {
            $output = ['status'=>'OK'];
            
            $token = $request->get("token");
            $userData = json_decode($rsession->get($token));
            
            
            $order = new Order($userData->id, $em);
            $orderEntity = $order->create();
            
            $output['order'] = [
                'orderId' => $orderEntity->getId(),
                'status' => $orderEntity->getStatus()
            ];
            
            return new Response($output);
        } catch (TokenExpiredException $e) {
            $output['status'] = 'ERROR';
            $output['msg'] = $e->getMessage();
            
            return new Response($output, 401);
        } catch (OrderBaseException $e) {
            $logger->error($e->getMessage());
            
            $output['status'] = 'ERROR';
            $output['msg'] = 'cannot crate order';
            
            return new Response($output, 503);
        } catch (\Exception $e) {
            $logger->critical($e->getMessage());
            
            $output['status'] = 'ERROR';
            $output['msg'] = 'internal error';
            
            return new Response($output, 500);
        }
    }
    
    /**
     * @Route("/order/cancel/{orderId}/", name="ordercancel", methods={"POST"})
     */
    public function cancel($orderId, RedisSession $rsession, Request $request, EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $output = ['status'=>'OK'];
            
            $token = $request->get("token");
            
            $userData = json_decode($rsession->get($token));
            
            
            $order = new Order($userData->id, $em);
            $order->setStatus(OrderStatus::STATUS_CANCELED, $orderId);
            
            return new Response($output);
        } catch (TokenExpiredException $e) {
            $output['status'] = 'ERROR';
            $output['msg'] = $e->getMessage();
            
            return new Response($output, 401);
        } catch (OrderNotFoundException $e) {
            $logger->error($e->getMessage());
            
            $output['status'] = 'ERROR';
            $output['msg'] = $e->getMessage();
            
            return new Response($output, 503);
        } catch (\Exception $e) {
            $logger->critical($e->getMessage());
            
            $output['status'] = 'ERROR';
            $output['msg'] = 'internal error';
            
            return new Response($output, 500);
        }
    }
    
    /**
     * @Route("/order/status/{orderId}/", name="ordergetstatus", methods={"GET"})
     */
    public function getStatus($orderId, RedisSession $rsession, Request $request, EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $output = ['status'=>'OK'];
            
            $token = $request->get("token");
            
            $userData = json_decode($rsession->get($token));
            
            
            $order = new Order($userData->id, $em);
            $status = $order->getStatus($orderId);
            
            $output['order']['status'] = $status;
            
            return new Response($output);
        } catch (TokenExpiredException $e) {
            $output['status'] = 'ERROR';
            $output['msg'] = $e->getMessage();
            
            return new Response($output, 401);
        } catch (OrderNotFoundException $e) {
            $logger->error($e->getMessage());
            
            $output['status'] = 'ERROR';
            $output['msg'] = $e->getMessage();
            
            return new Response($output, 503);
        } catch (\Exception $e) {
            $logger->critical($e->getMessage());
            
            $output['status'] = 'ERROR';
            $output['msg'] = 'internal error';
            
            return new Response($output, 500);
        }
    }
}