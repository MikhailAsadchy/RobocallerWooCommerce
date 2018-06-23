<?

namespace rcaller\wooCommerceAdapter;

use rcaller\lib\adapterInterfaces\Logger;
use rcaller\lib\client\RCallerClient;

class RCallerPlaceOrderHandler
{
    /**
     * @var RCallerClient
     */
    private $rCallerClient;
    /**
     * @var Logger
     */
    private $logger;

    public function __construct($rCallerClient)
    {
        $this->rCallerClient = $rCallerClient;
    }

    public function sendOrderToRCaller($order_id, $posted_data, $order)
    {
        $address = $this->resolveAddress($order);

        if ($address != null) {
            $externalOrderId = $order->id;
            $total = $order->data["total"];
            $entries = $order->items;
            $addressLine = $address["address_1"];
            $phone = $address["phone"];
            $customerName = $this->getCustomerName($address);
            $currency = $order->data["currency"];

            $this->rCallerClient->sendOrderToRCaller($externalOrderId, $total, $entries, $addressLine, $phone, $customerName, $currency);
        }
    }

    private function resolveAddress($order)
    {
        $billingAddress = $order->data["billing"];
        if ($billingAddress != null) {
            return $billingAddress;
        } else {
            $shippingAddress = $order->data["shipping"];
            if ($shippingAddress != null) {
                return $shippingAddress;
            } else {
                $this->logger->log("error", "Unable to retrieve billing or shipping address from order with code " . $order->id);
                return null;
            }
        }
    }

    private function getCustomerName($address)
    {
        return $address["first_name"] . $address["last_name"];
    }
}
