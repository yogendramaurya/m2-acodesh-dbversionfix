<?php
namespace Acodesh\DbVersionFix\Plugin\Magento\Framework\Module;

use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class DbVersionInfo
{

	/**
     * @var ModuleListInterface
     */
    private $moduleList;
    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * DbVersionInfoPlugin constructor.
     * @param ModuleListInterface $moduleList
     * @param ResourceInterface $moduleResource
     */
    public function __construct(
        ModuleListInterface $moduleList,
        ResourceInterface $moduleResource
    ) {
        $this->moduleList = $moduleList;
        $this->moduleResource = $moduleResource;
    }

    /**
     * @param $subject
     * @param $proceed
     * @param $moduleName
     * @return bool
     */
    public function aroundIsSchemaUpToDate($subject, $proceed, $moduleName)
    {
        $result = $proceed($moduleName);
        if (!$result) {
            $dbVer = $this->moduleResource->getDbVersion($moduleName);
            $result = $this->isModuleVersionLower($moduleName, $dbVer);
        }
        return $result;
    }

    /**
     * @param $subject
     * @param $proceed
     * @param $moduleName
     * @return bool
     */
    public function aroundIsDataUpToDate($subject, $proceed, $moduleName)
    {
        $result = $proceed($moduleName);
        if (!$result) {
            $dbVer = $this->moduleResource->getDataVersion($moduleName);
            $result = $this->isModuleVersionLower($moduleName, $dbVer);
        }
        return $result;
    }

    /**
     * @param $moduleName
     * @param $version
     * @return bool
     */
    private function isModuleVersionLower($moduleName, $version)
    {
        $module = $this->moduleList->getOne($moduleName);
        $configVer = $module['setup_version'];
        return ($version !== false
            && version_compare($configVer, $version) === ModuleDataSetupInterface::VERSION_COMPARE_LOWER);
    }

}
