<?php

namespace rcaller\lib\plugin;

use rcaller\lib\constants\RCallerConstants;

class RCallerPluginManager
{
    private $optionRepository;
    private $eventService;
    private $rCallerClient;

    public function __construct($optionRepository, $eventService, $rCallerClient)
    {
        $this->optionRepository = $optionRepository;
        $this->eventService = $eventService;
        $this->rCallerClient = $rCallerClient;
    }

    public function addOptions()
    {
        $this->optionRepository->addOrUpdateOption(RCallerConstants::USER_NAME_OPTION, "");
        $this->optionRepository->addOrUpdateOption(RCallerConstants::PASSWORD_OPTION, "");
    }

    public function removeOptions()
    {
        $this->optionRepository->removeOption(RCallerConstants::USER_NAME_OPTION);
        $this->optionRepository->removeOption(RCallerConstants::PASSWORD_OPTION);
    }

    public function subscribePlaceOrderEvent()
    {
        $this->eventService->subscribePlaceOrderEvent($this->rCallerClient);
    }

    public function unsubscribePlaceOrderEvent()
    {
        $this->eventService->unsubscribePlaceOrderEvent();
    }
}
