<?

namespace rcaller\adapter;
use rcaller\lib\adapterInterfaces\EventService;
use rcaller\wooCommerceAdapter\RCallerPlaceOrderHandler;

class WooCommerceEventService implements EventService
{

    public function subscribePlaceOrderEvent($rcallerClient)
    {
        $placeOrderHandler = new RCallerPlaceOrderHandler($rcallerClient);
        add_action('woocommerce_checkout_order_processed', array($placeOrderHandler, 'sendOrderToRCaller'), 10, 3);
    }

    public function unsubscribePlaceOrderEvent()
    {
        // we do not need to unsubscribe, cause wordpress invokes hook registration process on each request
    }
}
