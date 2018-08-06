<?php
//https://thierry-godin.developpez.com/openerp/openerp-xmlrpc-php-en/
require_once __DIR__.'/vendor/autoload.php';
$GLOBALS['xmlrpc_internalencoding']='UTF-8';

use PhpXmlRpc\Value;
use PhpXmlRpc\Request;
use PhpXmlRpc\Client;
use Ripcord\Ripcord;

// $username = 'shashank@ajency.in';
// $password = 'Ajency#123';
// $db = 'odoo_trial';
// $url = 'http://localhost:8069';

$username = 'admin_magento';
$password = 'LyraOmniRetail@987';
$db = 'KSS_Prod';
$url = 'https://indrajal.kidsuperstore.in';

$common = ripcord::client("$url/xmlrpc/2/common");

$uid = $common->authenticate($db, $username, $password, array());
$models = ripcord::client("$url/xmlrpc/2/object");

// $ids = $models->execute_kw($db, $uid, $password,
//   'product.public.category', 'search', [[]]);
// $response = $records = $models->execute_kw($db, $uid, $password,
//     'product.public.category', 'read', array($ids));

// $ids = $models->execute_kw($db, $uid, $password,
//   'product.template', 'search', [[]]);
// $response = $records = $models->execute_kw($db, $uid, $password,
//     'product.template', 'read', array($ids));

$ids = $models->execute_kw($db, $uid, $password,
  'product.template', 'search', [[]],['offset'=>3, 'limit'=> 20]);
$response = $records = $models->execute_kw($db, $uid, $password,
    'product.template', 'read', array(984));
echo '<img src="data:image/jpeg;base64,'.$records[0]['image'].'">';
echo '<pre>';
print_r($ids);
print_r($response);
echo "</pre>";
