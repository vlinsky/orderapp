<?php
namespace App\Orders;

use App\Config\Config;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Payment class
 * 
 * make http calls to payment service
 */
class Payment
{
    /**
     * HttpClient
     * 
     * @var HttpClient
     */
    private $httpClient;
    
    /**
     * Payment url
     * 
     * @var string
     */
    private $paymetnUrl;
    
    /**
     * Declinet payment status const
     */
    private const DECLINED_PAYMENT_STATUS = 'declined';
    
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->paymetnUrl = Config::getPaymentUrl();
    }
    
    public function makePayment(int $orderId, int $userId, string $userToken)
    {
        $response = $this->httpClient->request('POST', str_replace('{orderId}', $orderId, $this->paymetnUrl), [
            'body' => [
                'token'=>$userToken,
                'userId'=>$userId
                ]
        ]);
        
        $responseData = json_decode($response->getContent());
        
        if ($responseData && isset($responseData->order->status)) {
            return $this->mapToOrderStatus($responseData->order->status);
        } else {
            throw new PaymentException('payment service unaviable');
        }
    }
    /**
     * Map payments status to order status
     * 
     * @param string $status
     * @return string
     */
    private function mapToOrderStatus(string $status)
    {
        if ($status == self::DECLINED_PAYMENT_STATUS) {
            return OrderStatus::STATUS_CANCELED;
        } else {
            return OrderStatus::STATUS_CONFIRMED;
        }
    }
}