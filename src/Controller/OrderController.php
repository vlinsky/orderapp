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
use App\Config\ResponseStatus;
use App\Config\Error;
use Symfony\Component\HttpClient\HttpClient;
use App\Orders\Payment;
use App\Orders\PaymentException;


/**
 * OrderController
 * 
 * Order REST API
 */
class OrderController extends AbstractController
{
    /**
     * Create order REST api
     * 
     * usage : /order/create/
     * method : POST 
     * params : 
     *      token - user access token
     * 
     * @Route("/order/create/", name="ordercreate", methods={"POST"})
     */
    public function create(RedisSession $rsession, Request $request, EntityManagerInterface $em, LoggerInterface $logger)
    {   
        try {
            $output = ['status'=>ResponseStatus::STATUS_OK];
            
            $token = $request->get("token");
            $userData = json_decode($rsession->get($token));
            
            
            $order = new Order($userData->id, $em);
            $orderEntity = $order->create();
            
            $payment = new Payment(HttpClient::create());
            $newOrderStatus = $payment->makePayment($orderEntity->getId(), $orderEntity->getUserId(), $token);
            
            $order->setStatus($newOrderStatus, $orderEntity->getId());
            
            $output['order'] = [
                'orderId' => $orderEntity->getId(),
                'status' => $newOrderStatus
            ];
            
            return new Response($output);
        } catch (TokenExpiredException $e) {
            $output['status'] = ResponseStatus::STATUS_ERROR;
            $output['msg'] = $e->getMessage();
            
            return new Response($output, 401);
        } catch (OrderBaseException $e) {
            $logger->error($e->getMessage());
            
            $output['status'] = ResponseStatus::STATUS_ERROR;
            $output['msg'] = Error::CANNOT_CREATE_ORDER_ERROR;
            
            return new Response($output, 503);
        } catch (PaymentException $e) {
            $logger->error($e->getMessage());
            
            $output['status'] = ResponseStatus::STATUS_ERROR;
            $output['msg'] = Error::CANNOT_CREATE_ORDER_ERROR;
            
            //here should be done order rollback
            return new Response($output, 503);
        } catch (\Exception $e) {
            $logger->critical($e->getMessage());
            
            $output['status'] = ResponseStatus::STATUS_ERROR;
            $output['msg'] = Error::INTERNAL_ERROR.$e->getMessage();
            
            return new Response($output, 500);
        }
    }
    
    /**
     * Cancel order REST api
     * 
     * usage : /order/cancel/{orderId}/ 
     *      {orderId} - should be replaced with order id
     * method : POST 
     * params : 
     *      token - user access token
     *      
     * @Route("/order/cancel/{orderId}/", name="ordercancel", methods={"POST"})
     */
    public function cancel($orderId, RedisSession $rsession, Request $request, EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $output = ['status'=>ResponseStatus::STATUS_OK];
            
            $token = $request->get("token");
            
            $userData = json_decode($rsession->get($token));
            
            
            $order = new Order($userData->id, $em);
            $order->setStatus(OrderStatus::STATUS_CANCELED, $orderId);
            
            return new Response($output);
        } catch (TokenExpiredException $e) {
            $output['status'] = ResponseStatus::STATUS_ERROR;
            $output['msg'] = $e->getMessage();
            
            return new Response($output, 401);
        } catch (OrderNotFoundException $e) {
            $logger->error($e->getMessage());
            
            $output['status'] = ResponseStatus::STATUS_ERROR;
            $output['msg'] = $e->getMessage();
            
            return new Response($output, 503);
        } catch (\Exception $e) {
            $logger->critical($e->getMessage());
            
            $output['status'] = ResponseStatus::STATUS_ERROR;
            $output['msg'] = Error::INTERNAL_ERROR;
            
            return new Response($output, 500);
        }
    }
    
    /**
     * Get order status REST api
     * 
     * usage : /order/status/{orderId}/ 
     *      {orderId} - should be replaced with order id
     * method : GET 
     * params : 
     *      token - user access token
     *      
     * @Route("/order/status/{orderId}/", name="ordergetstatus", methods={"GET"})
     */
    public function getStatus($orderId, RedisSession $rsession, Request $request, EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $output = ['status'=>ResponseStatus::STATUS_OK];
            
            $token = $request->get("token");
            
            $userData = json_decode($rsession->get($token));
            
            
            $order = new Order($userData->id, $em);
            $status = $order->getStatus($orderId);
            
            $output['order']['status'] = $status;
            
            return new Response($output);
        } catch (TokenExpiredException $e) {
            $output['status'] = ResponseStatus::STATUS_ERROR;
            $output['msg'] = $e->getMessage();
            
            return new Response($output, 401);
        } catch (OrderNotFoundException $e) {
            $logger->error($e->getMessage());
            
            $output['status'] = ResponseStatus::STATUS_ERROR;
            $output['msg'] = $e->getMessage();
            
            return new Response($output, 503);
        } catch (\Exception $e) {
            $logger->critical($e->getMessage());
            
            $output['status'] = ResponseStatus::STATUS_ERROR;
            $output['msg'] = Error::INTERNAL_ERROR;
            
            return new Response($output, 500);
        }
    }
}