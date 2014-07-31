<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

class oxModuleMetadataAgainstShopValidator implements oxIModuleValidator
{
    /**
     * Validates module metadata.
     * Return true if module metadata is valid.
     * Return false if module metadata is not valid, or if metadata file does not exist.
     *
     * @param oxModule $oModule object to validate metadata.
     *
     * @return bool
     */
    public function validate(oxModule $oModule)
    {

        $blModuleExtensionsMatchShopInformation = $this->_moduleExtensionsInformationExistsInShop($oModule);
        $blModuleFilesMatchShopInformation = $this->_moduleFilesInformationExistInShop($oModule);

        return $blModuleExtensionsMatchShopInformation && $blModuleFilesMatchShopInformation;
    }

    /**
     * Check if all module extensions exists in shop information.
     *
     * @param oxModule $oModule
     *
     * @return bool
     */
    private function _moduleExtensionsInformationExistsInShop(oxModule $oModule)
    {
        $aModuleExtensions = $oModule->getExtensions();

        /** @var oxModuleInstaller $oModuleInstaller */
        $oModuleInstaller = oxNew('oxModuleInstaller');
        $aShopInformationAboutModulesExtendedClasses = $oModuleInstaller->getModulesWithExtendedClass();

        foreach ($aModuleExtensions as $sExtendedClassName => $sModuleExtendedClassPath) {
            $aExtendedClassInfo = $aShopInformationAboutModulesExtendedClasses[$sExtendedClassName];
            if (is_null($aExtendedClassInfo) || !is_array($aExtendedClassInfo)) {
                return false;
            }
            if (!in_array($sModuleExtendedClassPath, $aExtendedClassInfo)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if all module files exists in shop.
     *
     * @param oxModule $oModule
     *
     * @return bool
     */
    private function _moduleFilesInformationExistInShop(oxModule $oModule)
    {
        $aModuleFiles = $oModule->getFiles();

        /** @var oxModuleList $oModuleList */
        $oModuleList = oxNew('oxModuleList');
        $aShopInformationAboutModulesFiles = $oModuleList->getModuleFiles();

        $aMissingFiles = array_diff($aModuleFiles, $aShopInformationAboutModulesFiles);
        return (count($aMissingFiles)) === 0;
    }
}
