<?
namespace rcaller\lib\dto;
class RCallerOrderDtoBuilder
{
    private $channelNameProvider;
    private $entryAsStringFormatter;

    public function __construct($channelNameProvider, $entryAsStringFormatter)
    {
        $this->channelNameProvider = $channelNameProvider;
        $this->entryAsStringFormatter = $entryAsStringFormatter;
    }

    public function buildOrderDto($price, $entries, $customerAddress, $customerPhone, $customerName, $priceCurrency)
    {
        $entriesAsString = $this->entryAsStringFormatter->getEntriesAsString($entries);

        $data = array(
            'price' => $price,
            'entries' => $entriesAsString,
            'customerAddress' => $customerAddress,
            'customerPhone' => $customerPhone,
            'customerName' => $customerName,
            'priceCurrency' => $priceCurrency,
            'channel' => $this->channelNameProvider->getChannelName());
        return $data;
    }
}

?>