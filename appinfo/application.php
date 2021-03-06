<?php
/**
 * Nextcloud - Github
 *
 *
 * @author Julien Veyssier <eneiluj@posteo.net>
 * @copyright Julien Veyssier 2020
 */

namespace OCA\Github\AppInfo;

use OCP\IContainer;

use OCP\AppFramework\App;
use OCP\AppFramework\IAppContainer;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;

use OCA\Github\Controller\PageController;
use OCA\Github\Dashboard\GithubPanel;

/**
 * Class Application
 *
 * @package OCA\Github\AppInfo
 */
class Application extends App implements IBootstrap {

    /**
     * Constructor
     *
     * @param array $urlParams
     */
    public function __construct(array $urlParams = []) {
        parent::__construct('github', $urlParams);

        $container = $this->getContainer();
    }

    public function register(IRegistrationContext $context): void {
        $context->registerDashboardPanel(GithubPanel::class);
    }

    public function boot(IBootContext $context): void {
    }
}

