<?

namespace rcaller\wooCommerceAdapter;

class RCallerPlaceOrderHandler
{
    private $rCallerClient;

    public function __construct($rCallerClient)
    {
        $this->rCallerClient = $rCallerClient;
    }


    public function sendOrderToRCaller($order_id, $posted_data, $order)
    {
        $address = $this->resolveAddress($order);

        $total = $order->data["total"];
        $entries = $order->items;
        $addressLine = $address["address_1"];
        $phone = $address["phone"];
        $customerName = $this->getCustomerName($address);
        $currency = $order->data["currency"];

        $this->rCallerClient->sendOrderToRCaller($total, $entries, $addressLine, $phone, $customerName, $currency);
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
                throw new Exception("Unable to retrieve billing or shipping address from order with code " . $order->id);
            }
        }
    }

    private function getCustomerName($address)
    {
        return $address["first_name"] . $address["last_name"];
    }
}
