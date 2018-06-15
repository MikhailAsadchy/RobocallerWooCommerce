<?

namespace rcaller\lib\ioc;


use rcaller\lib\client\RCallerClient;
use rcaller\lib\dao\credentials\CredentialsManager;
use rcaller\lib\dto\formatter\EntryAsStringFormatter;
use rcaller\lib\dto\RCallerOrderDtoBuilder;
use rcaller\lib\plugin\RCallerPluginManager;

class RCallerDependencyContainer
{
    // adapter
    private $channelNameProvider;
    private $eventService;
    private $logger;
    private $optionsRepository;
    private $orderEntryFieldResolver;

    // lib
    private $credentialsManager;
    private $rCallerOrderDtoBuilder;
    private $rCallerClient;
    private $rCallerPluginManager;
    private $entryAsStringFormatter;

    public function __construct($eventService, $logger, $optionsRepository, $channelNameProvider, $orderEntryFieldResolver)
    {
        // rcaller to wooCommerce adapter
        $this->eventService = $eventService;
        $this->logger = $logger;
        $this->optionsRepository = $optionsRepository;
        $this->channelNameProvider = $channelNameProvider;
        $this->orderEntryFieldResolver = $orderEntryFieldResolver;

        // lib dependencies
        $this->credentialsManager = new CredentialsManager($optionsRepository);
        $this->rCallerClient = new RCallerClient($this->credentialsManager, $this->logger, $this->rCallerOrderDtoBuilder);

        $this->entryAsStringFormatter = new EntryAsStringFormatter($orderEntryFieldResolver);
        $this->rCallerOrderDtoBuilder = new RCallerOrderDtoBuilder($channelNameProvider, $this->entryAsStringFormatter);
        $this->rCallerPluginManager = new RCallerPluginManager($optionsRepository, $eventService, $this->rCallerClient);
    }

    public function getRCallerClient()
    {
        return $this->rCallerClient;
    }

    public function getPluginManager()
    {
        return $this->rCallerPluginManager;
    }

}

?>