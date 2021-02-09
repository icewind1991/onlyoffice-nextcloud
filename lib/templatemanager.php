<?php
/**
 *
 * (c) Copyright Ascensio System SIA 2020
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace OCA\Onlyoffice;

use OCP\Files\Folder;
use OCP\Files\File;

/**
 * Template manager
 *
 * @package OCA\Onlyoffice
 */
class TemplateManager {

    /**
     * Get template
     *
     * @param string $name - file name
     *
     * @return string
     */
    public static function GetTemplate(string $name) {
        $ext = strtolower("." . pathinfo($name, PATHINFO_EXTENSION));

        $lang = \OC::$server->getL10NFactory("")->get("")->getLanguageCode();

        $templatePath = self::getTemplatePath($lang, $ext);
        if (!file_exists($templatePath)) {
            $lang = "en";
            $templatePath = self::getTemplatePath($lang, $ext);
        }

        $template = file_get_contents($templatePath);
        return $template;
    }

    /**
     * Get global template directory
     *
     * @return Folder
     */
    public static function GetGlobalTemplateDir() {
        $rootFolder = \OC::$server->getRootFolder();

        $appData = $rootFolder->get("appdata_" . \OC::$server->getConfig()->GetSystemValue("instanceid", null));

        $appDir = $appData->nodeExists("onlyoffice") ? $appData->get("onlyoffice") : $appData->newFolder("onlyoffice");
        $templateDir = $appDir->nodeExists("template") ? $appDir->get("template") : $appDir->newFolder("template");

        return $templateDir;
    }

    /**
     * Get global template list
     * 
     * @param string $template - mimetype of the template
     *
     * @return array
     */
    public static function GetGlobalTemplates($mimetype) {
        $templates = [];

        $templateFiles = self::GetGlobalTemplateDir()->searchByMime($mimetype);
        if (count($templateFiles) > 0) {
            $templates = array_merge($templates, $templateFiles);
        }

        return $templates;
    }

    /**
     * Get global template
     * 
     * @param string $templateId - identifier of the template
     *
     * @return File
     */
    public static function GetGlobalTemplate($templateId) {
        $logger = \OC::$server->getLogger();

        try {
            $files = self::GetGlobalTemplateDir()->getById($templateId);
        } catch (\Exception $e) {
            $logger->logException($e, ["message" => "GetGlobalTemplate: $templateId", "app" => "onlyoffice"]);
            return null;
        }

        if (empty($files)) {
            $logger->info("Template not found: $templateId", ["app" => "onlyoffice"]);
            return null;
        }

        return $files[0];
    }

    /**
     * Get template path
     *
     * @param string $lang - language
     * @param string $ext - file extension
     *
     * @return string
     */
    private static function GetTemplatePath(string $lang, string $ext) {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . "new" . $ext;
    }
}
