<?

namespace rcaller\adapter;
use rcaller\lib\adapterInterfaces\OrderEntryFieldResolver;

class WooCommerceOrderEntryFieldResolver implements OrderEntryFieldResolver
{
    public function getName($item)
    {
        return $item->get_name();
    }

    public function getQuantity($item)
    {
        return intval($item->get_quantity());
    }

    public function getUnit($item)
    {
        return "шт";
    }
}
