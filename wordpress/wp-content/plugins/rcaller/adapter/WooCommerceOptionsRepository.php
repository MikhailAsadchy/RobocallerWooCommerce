<?

namespace rcaller\adapter;
use rcaller\lib\adapterInterfaces\OptionRepository;

class WooCommerceOptionsRepository implements OptionRepository
{
    public function addOrUpdateOption($name, $value)
    {
        update_option($name, $value);
    }

    public function removeOption($name)
    {
        delete_option($name);
    }

    public function getOption($name)
    {
        return get_option($name);
    }
}
