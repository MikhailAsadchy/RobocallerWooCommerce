<?

class RCallerOrderSender
{

    /**
     * @param $order_id
     * @param $posted_data
     * @param $order
     * @throws Exception
     */
    public function sendOrderToRCaller($order_id, $posted_data, $order)
    {
        $entriesAsString = $this->getEntriesAsString($order);
        $address = $this->resolveAddress($order);

        $customerName = $this->getCustomerName($address);

        $data = array(
            'price' => $order->data["total"],
            'entries' => $entriesAsString,
            'customerAddress' => $address["address_1"],
            'customerPhone' => $address["phone"],
            'customerName' => $customerName,
            'priceCurrency' => $order->data["currency"],
            'channel' => "WooCommerce");

        $userName = get_option(RCallerAdminConstants::USER_NAME_OPTION);
        $password = get_option(RCallerAdminConstants::PASSWORD_OPTION);

        $this->sendOrderToRCallerInternal($data, $userName, $password);
    }

    /**
     * @param $data
     * @param $username
     * @param $password
     * @return int
     */
    public function sendOrderToRCallerInternal($data, $username, $password)
    {
        $rcallerConfig = parse_ini_file("rcaller-config.ini");
        $curl = curl_init($rcallerConfig["rcaller.url"]);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $rcallerConfig["rcaller.connectionTimeOut"]);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $httpCode;
    }

    private function getEntriesAsString($order)
    {
        $entriesAsStrings = [];
        foreach ($order->items as $item) {
            $name = $item->get_name();
            $quantity = intval($item->get_quantity());
            $unit = "шт";
            $entryString = $name . " " . $quantity . " " . $unit . ".";
            array_push($entriesAsStrings, $entryString);
        }
        return join(" | ", $entriesAsStrings);
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
        return $address["first_name"] . " " . $address["last_name"];
    }
}
?>