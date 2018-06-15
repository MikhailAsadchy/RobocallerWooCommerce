<?

namespace rcaller\lib\adapterInterfaces;
interface EventService
{
    public function subscribePlaceOrderEvent($rcallerClient);

    public function unsubscribePlaceOrderEvent();
}
