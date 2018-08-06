<?php
require_once __DIR__.'/vendor/autoload.php';

use Ripcord\Ripcord;

$username = 'admin_magento';
$password = 'LyraOmniRetail@987';
$db = 'KSS_Prod';
$url = 'https://indrajal.kidsuperstore.in';

$common = ripcord::client("$url/xmlrpc/2/common");

$uid = $common->authenticate($db, $username, $password, array());
$models = ripcord::client("$url/xmlrpc/2/object");

if(isset($_GET['id'])) $product_id = (int)$_GET['id'];

$records = $models->execute_kw($db, $uid, $password,
    'product.template', 'read', array($product_id));
$product = $records[0];
echo '<pre>';
print_r($records);
echo "</pre>";
?>
