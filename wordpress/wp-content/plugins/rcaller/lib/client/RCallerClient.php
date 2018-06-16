<?

namespace rcaller\lib\client;

use Exception;
use rcaller\lib\validation\ValidationResult;

class RCallerClient
{
    const ENTRIES_DELIMITER = " | ";

    private $credentialsManager;
    private $logger;
    private $rCallerOrderDtoBuilder;

    function __construct($credentialsManager, $logger, $rCallerOrderDtoBuilder)
    {
        $this->credentialsManager = $credentialsManager;
        $this->logger = $logger;
        $this->rCallerOrderDtoBuilder = $rCallerOrderDtoBuilder;
    }

    public function sendOrderToRCaller($total, $entries, $addressLine, $phone, $customerName, $currency)
    {
        $data = $this->rCallerOrderDtoBuilder->buildOrderDto($total,
            $entries, $addressLine, $phone, $customerName, $currency);
        $this->sendOrderToRCallerInternal($data);
    }

    private function sendOrderToRCallerInternal($data)
    {
        try {
            $validationResult = $this->processAndValidateRequestBody($data);
            
            if (!$validationResult->hasErrors()) {
                $username = $this->credentialsManager->getUserName();
                $password = $this->credentialsManager->getPassword();
                $httpCode = $this->doSendOrderToRCaller($data, $username, $password);
                $this->logRCallerResponse($httpCode);
            } else {
                $this->logger->log("error", $validationResult);
            }
        } catch (Exception $e) {
            $this->logger->log("error", $e->getMessage());
        }
    }

    /**
     * @param $data
     * @param $username
     * @param $password
     * @return int
     */
    private function doSendOrderToRCaller($data, $username, $password)
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

    /**
     * @param $username
     * @param $password
     * @return int
     */
    public static function checkRCallerCredentials($username, $password)
    {
        $rcallerConfig = parse_ini_file("rcaller-config.ini");
        $curl = curl_init($rcallerConfig["rcaller.ping.url"]);
        curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $rcallerConfig["rcaller.connectionTimeOut"]);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $httpCode;
    }

    private function validateCustomerPhone($data, $validationResult)
    {
        $customerPhone = $data["customerPhone"];
        $phonePattern = "/^((\+7)([0-9]){7,14})$/";
        $matches = preg_match($phonePattern, $customerPhone);
        if (!$matches) {
            $validationResult->addError("customerPhone", "phone must match regular expression " . $phonePattern);
        }
    }

    private function processEntries($data)
    {
        $entries = $data["entries"];
        $length = strlen($entries);
        if ($length > 1024) {
            $data["entries"] = substr($entries, 0, 1024);
        }
    }

    private function logRCallerResponse($httpCode)
    {
        if ($httpCode == 400) {
            $this->logger->log("error", "RCaller: bad request was sent");
        } else if ($httpCode == 401) {
            $this->logger->log("error", "RCaller: bad credentials");
        } else if ($httpCode == 403) {
            $this->logger->log("error", "RCaller: negative balance");
        }
    }

    private function validatePriceCurrency($data, $validationResult)
    {
        $priceCurrency = $data["priceCurrency"];
        $isEmpty = empty($priceCurrency);
        if ($isEmpty) {
            $validationResult->addError("priceCurrency", "priceCurrency field should not be empty");
        }
        $length = strlen($priceCurrency);
        if ($length > 5) {
            $validationResult->addError("priceCurrency", "priceCurrency field length should be 1-5");
        }
    }

    private function validatePrice($data, $validationResult)
    {
        $price = $data["price"];
        $isNumeric = is_numeric($price);
        if (!$isNumeric) {
            $validationResult->addError("price", "price field should be a number, but was: " . $price);
        }
    }

    private function validateCustomerNameField($data, $validationResult)
    {
        $customerName = $data["customerName"];
        $isEmpty = empty($customerName);
        if ($isEmpty) {
            $validationResult->addError("customerName", "customerName field should not be empty");
        }
        $length = strlen($customerName);
        if ($length > 255) {
            $validationResult->addError("customerName", "customerName field length should be 1-255");
        }
    }

    private function validateCustomerAddressField($data, $validationResult)
    {
        $customerAddress = $data["customerAddress"];
        $isEmpty = empty($customerAddress);
        if ($isEmpty) {
            $validationResult->addError("customerAddress", "customerAddress field should not be empty");
        }
        $length = strlen($customerAddress);
        if ($length > 255) {
            $validationResult->addError("customerAddress", "customerAddress field length should be 1-255");
        }
    }

    private function validateEntriesField($data, $validationResult)
    {
        $entries = $data["entries"];
        $isEmpty = empty($entries);
        if ($isEmpty) {
            $validationResult->addError("entries", "entries field should not be empty");
        }
        $length = strlen($entries);
        if ($length > 1024) {
            $validationResult->addError("entries", "entries field length should be 1-1024");
        }
    }

    /**
     * @param $data
     */
    private function sanitizeCustomerPhone($data)
    {
        $customerPhone = $data["customerPhone"];
        $phoneChars = str_split($customerPhone);
        $phoneNumbers = array();
        foreach ($phoneChars as $char) {
            if (is_numeric($char)) {
                array_push($phoneNumbers, $char);
            }
        }
        $phoneNumber = "+" . implode($phoneNumbers);
        $data["customerPhone"] = $phoneNumber;
    }

    private function processRequestBody($data)
    {
        $this->sanitizeCustomerPhone($data);
        $this->processEntries($data);
    }

    private function validateRequestBody($data)
    {
        $validationResult = new ValidationResult();
        // todo[Mikhail_Asadchy] comment out during pre-prod testing
//        $this->validateCustomerPhone($data, $validationResult);
        $this->validatePrice($data, $validationResult);
        $this->validateEntriesField($data, $validationResult);
        $this->validateCustomerAddressField($data, $validationResult);
        $this->validateCustomerNameField($data, $validationResult);
        $this->validatePriceCurrency($data, $validationResult);
        return $validationResult;
    }

    private function processAndValidateRequestBody($data)
    {
        $this->processRequestBody($data);
        $validationResult = $this->validateRequestBody($data);
        return $validationResult;
    }
}

?>