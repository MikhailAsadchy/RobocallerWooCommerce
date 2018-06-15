<?

namespace rcaller\adapter;
use rcaller\lib\util\StrictImporter;

class RCallerAdapterImport
{
    public static function importRCallerAdapter()
    {
        $files = array();

        $currentFileLocation = dirname(__FILE__);
        array_push($files, $currentFileLocation . "/WooCommerceChannelNameProvider.php");
        array_push($files, $currentFileLocation . "/WooCommerceEventService.php");
        array_push($files, $currentFileLocation . "/WooCommerceLogger.php");
        array_push($files, $currentFileLocation . "/WooCommerceOptionsRepository.php");
        array_push($files, $currentFileLocation . "/WooCommerceOrderEntryFieldResolver.php");

        StrictImporter::importFiles($files);
    }
}
