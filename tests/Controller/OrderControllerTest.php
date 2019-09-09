<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Orders\OrderStatus;

class OrderControllerTest extends WebTestCase
{
    protected $client;
    protected $token;
    protected $domain = 'http://localhost:8000';
    protected $orderId;
    protected $createdStatus = 'created';
    
    protected function setUp()
    {
        $this->client = static::createClient();
        $this->client->request('GET', $this->domain.'/user/login/');
        
        $response = json_decode($this->client->getResponse()->getContent());
        
        $this->token = $response->token;
    }
    
    public function testCreate()
    {
        $this->client->request('POST', $this->domain.'/order/create/', array(
            'token' => $this->token
        ));
        
        $response = json_decode($this->client->getResponse()->getContent());
        
        $this->assertIsInt($response->order->orderId);
    }
    
    public function testGetStatus()
    {   
        $this->client->request('POST', $this->domain.'/order/create/', array(
            'token' => $this->token
        ));
        
        $responseOrderData = json_decode($this->client->getResponse()->getContent());
        
        $orderId = $responseOrderData->order->orderId;
        
        $getStatusUrl = $this->domain.sprintf('/order/status/%s/', $orderId).'?token='.$this->token;
        
        $this->assertIsInt($orderId);
        
        $this->client->request('GET', $getStatusUrl);
        
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(OrderStatus::STATUS_CREATED, $response->order->status);
    }
    
    public function testCancelOrder()
    {
        $this->client->request('POST', $this->domain.'/order/create/', array(
            'token' => $this->token
        ));
        
        $responseOrderData = json_decode($this->client->getResponse()->getContent());
        
        $orderId = $responseOrderData->order->orderId;
        
        $cancelOrderUrl = $this->domain.sprintf('/order/cancel/%s/', $orderId);
        $this->client->request('POST', $cancelOrderUrl, array(
            'token' => $this->token
        ));
        
        $getStatusUrl = $this->domain.sprintf('/order/status/%s/', $orderId).'?token='.$this->token;
        
        $this->client->request('GET', $getStatusUrl);
        
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(OrderStatus::STATUS_CANCELED, $response->order->status);
    }
}