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

namespace OCA\Onlyoffice\AppInfo;

use OC\EventDispatcher\SymfonyAdapter;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\DirectEditing\RegisterDirectEditorEvent;
use OCP\Util;
use OCP\IPreview;

use OCA\Viewer\Event\LoadViewer;

use OCA\Onlyoffice\AppConfig;
use OCA\Onlyoffice\DirectEditor;
use OCA\Onlyoffice\Hooks;
use OCA\Onlyoffice\Preview;

class Application extends App implements IBootstrap {

    /**
     * Application configuration
     *
     * @var AppConfig
     */
    public $appConfig;

    public function __construct(array $urlParams = []) {
        $appName = "onlyoffice";

        parent::__construct($appName, $urlParams);

        $this->appConfig = new AppConfig($appName);
    }

    public function register(IRegistrationContext $context): void {
        require_once __DIR__ . "/../3rdparty/jwt/BeforeValidException.php";
        require_once __DIR__ . "/../3rdparty/jwt/ExpiredException.php";
        require_once __DIR__ . "/../3rdparty/jwt/SignatureInvalidException.php";
        require_once __DIR__ . "/../3rdparty/jwt/JWT.php";
    }

    public function boot(IBootContext $context): void {

        $context->injectFn(function (SymfonyAdapter $eventDispatcher) {

            $eventDispatcher->addListener('OCA\Files::loadAdditionalScripts',
            function() {
                Util::addScript("onlyoffice", "desktop");
                Util::addScript("onlyoffice", "main");
    
                if ($this->appConfig->GetSameTab()) {
                    Util::addScript("onlyoffice", "listener");
                }
    
                Util::addStyle("onlyoffice", "main");
    
            });
            $eventDispatcher->addListener('OCA\Files_Sharing::loadAdditionalScripts',
            function() {
                if (!empty($this->appConfig->GetDocumentServerUrl())
                    && $this->appConfig->SettingsAreSuccessful()) {
                    Util::addScript("onlyoffice", "main");

                    if ($this->appConfig->GetSameTab()) {
                        Util::addScript("onlyoffice", "listener");
                    }

                    Util::addStyle("onlyoffice", "main");
                }
            });
            if (class_exists(LoadViewer::class)) {
                $eventDispatcher->addListener(LoadViewer::class,
                function () {
                    if (!empty($this->appConfig->GetDocumentServerUrl())
                        && $this->appConfig->SettingsAreSuccessful()
                        && $this->appConfig->isUserAllowedToUse()) {
                        Util::addScript("onlyoffice", "viewer");
                        Util::addScript("onlyoffice", "listener");

                        Util::addStyle("onlyoffice", "viewer");

                        $csp = new ContentSecurityPolicy();
                        $csp->addAllowedFrameDomain("'self'");
                        $cspManager = $this->getContainer()->getServer()->getContentSecurityPolicyManager();
                        $cspManager->addDefaultPolicy($csp);
                    }
                });
            }

            $container = $this->getContainer();

            $eventDispatcher->addListener(RegisterDirectEditorEvent::class,
            function (RegisterDirectEditorEvent $event) use ($container) {
                if (!empty($this->appConfig->GetDocumentServerUrl())
                    && $this->appConfig->SettingsAreSuccessful()) {
                    $editor = $container->query(DirectEditor::class);
                    $event->register($editor);
                }
            });

            $previewManager = $container->query(IPreview::class);
            $previewManager->registerProvider(Preview::getMimeTypeRegex(), function() use ($container) {
                return $container->query(Preview::class);
            });
        });

        Hooks::connectHooks();
    }
}
