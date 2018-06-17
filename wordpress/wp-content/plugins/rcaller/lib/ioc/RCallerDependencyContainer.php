<?

namespace rcaller\lib\ioc;


use rcaller\lib\client\RCallerClient;
use rcaller\lib\dao\credentials\CredentialsManager;
use rcaller\lib\dto\formatter\EntryAsStringFormatter;
use rcaller\lib\dto\RCallerOrderDtoBuilder;
use rcaller\lib\plugin\RCallerPluginManager;
use rcaller\lib\settings\RCallerSettingsPageRenderer;
use rcaller\lib\ui\RCallerFormHelper;

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
    private $rCallerSettingsPageRenderer;
    private $rCallerFormHelper;

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
        $this->entryAsStringFormatter = new EntryAsStringFormatter($orderEntryFieldResolver);
        $this->rCallerOrderDtoBuilder = new RCallerOrderDtoBuilder($channelNameProvider, $this->entryAsStringFormatter);
        $this->rCallerClient = new RCallerClient($this->credentialsManager, $this->logger, $this->rCallerOrderDtoBuilder);
        $this->rCallerFormHelper = new RCallerFormHelper($this->credentialsManager, $this->rCallerClient);
        $this->rCallerSettingsPageRenderer = new RCallerSettingsPageRenderer($this->credentialsManager, $this->rCallerClient, $this->rCallerFormHelper);

        $this->rCallerPluginManager = new RCallerPluginManager($optionsRepository, $eventService, $this->rCallerClient);
    }

    /**
     * @return RCallerClient
     */
    public function getRCallerClient()
    {
        return $this->rCallerClient;
    }

    /**
     * @return RCallerPluginManager
     */
    public function getPluginManager()
    {
        return $this->rCallerPluginManager;
    }

    /**
     * @return RCallerSettingsPageRenderer
     */
    public function getRCallerSettingsPageRenderer()
    {
        return $this->rCallerSettingsPageRenderer;
    }

    /**
     * @return RCallerFormHelper
     */
    public function getRCallerFormHelper()
    {
        return $this->rCallerFormHelper;
    }


}

?>