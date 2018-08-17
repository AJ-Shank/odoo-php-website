<?php
require_once __DIR__.'/vendor/autoload.php';
require_once('config.php');
use Ripcord\Ripcord;



$common = ripcord::client("$url/xmlrpc/2/common");

$uid = $common->authenticate($db, $username, $password, array());
$models = ripcord::client("$url/xmlrpc/2/object");


$limit = 10;
$offset = 15;
$sort = "id";


?>
<pre>
<?php

$conn = new mysqli("localhost","root", "root", "store");

$sql = "SELECT max(id) as id from pos_orders";
$result = $conn->query($sql);
if ( $row = $result->fetch_assoc()) {
    $first_id = (int)$row["id"];

} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    die();
}
print_r($first_id);
$first_id = 0;

$fields = [
	'amount_total',
	'lines',
	'store_id',
	'partner_id',
	'date_order'
];
do{
	$order_ids = $models->execute_kw($db, $uid, $password, 'pos.order', 'search_read', array(array(["id" ,">" ,$first_id])),
	array('fields'=> $fields, 'limit'=> $limit, 'order' => $sort));

	print_r($order_ids);

	foreach ($order_ids as $key => $value) {
		print_r($value);
		$json = mysqli_real_escape_string($conn,json_encode($value['lines']));
		$sql = "INSERT into pos_orders VALUES({$value["id"]}, {$value["amount_total"]},\"{$json}\", \"{$value['date_order']}\")";	
		// print_r($result);
		if ($conn->query($sql) === TRUE) {
			$first_id=$value['id'];
	    	echo "New record created successfully";
		} else {
		    echo "Error: " . $sql . "<br>" . $conn->error;
		    die();
		}
		
		if(!empty($value['lines'])){
			$order_lines = $models->execute_kw($db, $uid, $password, 'pos.order.line', 'read', array($value['lines']));
			// print_r($order_lines);
			foreach ($order_lines as $key => $value) {
				// echo $value['id']. " ".$value['display_name'];
				$sql = "INSERT into pos_orderlines VALUES( {$value["id"]}, \"{$value["display_name"]}\")";	
				$conn->query($sql);
			}
			// die();
		}
	}
}while(count($order_ids) == $limit);



$conn->close();


?>
	
</pre>