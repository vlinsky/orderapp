# orderapp
order microservice

##installation :

git clone https://github.com/vlinsky/orderapp.git  
cd orderappm  
composer install  
docker-compose up  

docker excec orderapp php /usr/src/bin/console doctrine:migrations:migrate  

change PAYMENT_URL in .env file  
  
##REST API

User login /user/login/   
	method : GET   
	return : json   
   
Create order /order/create/  
	method : POST  
	params :  
    		token - user access token  
    return : json  
  
 Cancel order /order/cancel/{orderId}/  
    {orderId} - should be replaced with order id  
    method : POST  
    params :  
    		token - user access token  
 	return : json  
 	  
 Get order status /order/status/{orderId}/   
     {orderId} - should be replaced with order id  
     method : GET  
     params :  
     	token - user access token  
     return : json  