<?

namespace rcaller\adapter;
use rcaller\lib\adapterInterfaces\ChannelNameProvider;

class WooCommerceChannelNameProvider implements ChannelNameProvider
{

    public function getChannelName()
    {
        return "WooCommerce";
    }
}
