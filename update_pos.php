<?php
require_once __DIR__.'/vendor/autoload.php';
require_once('config.php');
use Ripcord\Ripcord;
use Elasticsearch\ClientBuilder;

$client = ClientBuilder::create()->build();

$common = ripcord::client("$url/xmlrpc/2/common");
$uid = $common->authenticate($db, $username, $password, array());
$models = ripcord::client("$url/xmlrpc/2/object");


$limit = 100;
$sort = "id";


?>
<pre>
<?php

$params = [
    'index' => 'orders',
    'body' => [
        'aggs' => [
            'max_id' => [ 
            	'max' => [
            		'field' => 'order_id'],
            	],

        ]
    ]
];

$response = $client->search($params);
$first_id = (int) $response['aggregations']['max_id']['value'];
print_r($first_id);


$fields = [
	'amount_total',
	'lines',
	'store_id',
	'partner_id',
	'date_order'
];
$i = 0;
do{
	$order_ids = $models->execute_kw($db, $uid, $password, 'pos.order', 'search_read', array(array(["id" ,">" ,$first_id])),
	array('fields'=> $fields, 'limit'=> $limit, 'order' => $sort));


	foreach ($order_ids as $key => $order) {
		

		$total_qty = 0;
		if(!empty($order['lines'])){
			$order_lines = $models->execute_kw($db, $uid, $password, 'pos.order.line', 'read', array($order['lines']));

			foreach ($order_lines as $key => $value) {
				$total_qty += $value["qty"];
			}

		}

		$body = [
			"order_id" => $order["id"],
			"order_number" => "POS/ST/100003", 
			"order_date" => $order['date_order'], 
			"store_type" => "pos",
			"store_name" => $order['store_id'][1],
			"store_id"=> $order['store_id'][0], 
			"customer_id"=> $order['partner_id'][0], 
			"customer_name" => $order['partner_id'][1], 
			"total_amount" => $order["amount_total"],
			"sku_count" => count($order['lines']),
			"total_qty" => $total_qty,
			"suborders_count" => count($order['lines']),

		];
		$params = [
		    'index' => 'orders',
		    'type' => '_doc',
		    'body' => $body,
		];
		$response = $client->index($params);
		$i++;
		print_r($i);
		print_r($response);
		$first_id=$order['id'];
	}
}while(count($order_ids) == $limit);



$conn->close();


?>
	
</pre>