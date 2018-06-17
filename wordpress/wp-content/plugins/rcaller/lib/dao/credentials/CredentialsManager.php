<?

namespace rcaller\lib\dao\credentials;
use rcaller\lib\constants\RCallerConstants;

class CredentialsManager
{
    private $optionsRepository;

    public function __construct($optionsRepository)
    {
        $this->optionsRepository = $optionsRepository;
    }

    public function getUserName()
    {
        return $this->optionsRepository->getOption(RCallerConstants::USER_NAME_OPTION);
    }

    public function getPassword()
    {
        return $this->optionsRepository->getOption(RCallerConstants::PASSWORD_OPTION);
    }

    public function storeCredentials($userName, $password)
    {
        $this->optionsRepository->addOrUpdateOption(RCallerConstants::USER_NAME_OPTION, $userName);
        $this->optionsRepository->addOrUpdateOption(RCallerConstants::PASSWORD_OPTION, $password);
    }
}
