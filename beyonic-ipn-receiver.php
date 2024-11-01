<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
    
global $woocommerce;
$responce = json_decode(file_get_contents("php://input"));
if (!empty($responce)) {
    $data = $responce->data;
    $hook = $responce->hook;
    $event = $hook->event;
    if ( in_array($event, array('collection.received', 'collection.credited')) )
    {  
        // get order id from collection request
        $wc_beyonic = new Beyonic_Woo_Gw();
        $wc_beyonic->authorize_beyonic_gw();
        $collection_request = Beyonic_Collection_Request::get($data->collection_request->id);
        $order_id = intval($collection_request->metadata->order_id);
        $status = sanitize_text_field($data->status);
        $order = new WC_Order($order_id);
        if ($status == "successful")
        {
            if ($event == 'collection.received')
            {
                $order->update_status('processing');
            }
            else
            {
                $order->update_status('paid');
            }
        }
        else
        {
            $order->update_status('cancelled');
        }
    }
}

