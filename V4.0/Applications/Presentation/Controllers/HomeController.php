<?php

namespace APP_Presentation_Controller;

use API_Profiling_Controller\AuthenticationController;
use APP_Administration_Controller\Controller;
use APP_Presentation_Model\ConnectionModel;
use Exception;
use ReflectionException;
use TS_DependencyInjection\Classes\ServiceLocator;
use TS_Utility\Classes\UrlGenerator;
use TS_Utility\Classes\Utils;

class HomeController extends Controller
{
    private string $appVersion;
    private UrlGenerator $urlGenerator;
    private AuthenticationController $authenticationController;

    public function __construct(AuthenticationController $_authenticationController)
    {
        $this->urlGenerator = new UrlGenerator(dirname(__DIR__, 2).'\Assets\Data\json\config.json');
        parent::__construct($this->urlGenerator);

        // Set the Exception property
        $this->SetException();
        $AdministrationController = ServiceLocator::GetController('APP_Administration_Controller\AdministrationController');
        $this->appVersion = $AdministrationController->GetAppVersion();
        $this->authenticationController = $_authenticationController;
    }

    /* Actions */

    // Home page
    /**
     * @throws ReflectionException
     */
    public function Index(): void
    {
        $languages = $this->languageController->Get();
        $this->view('Home', ['languages' => $languages, 'appVersion' => $this->appVersion]);
    }

    /**
     * @throws ReflectionException
     */
    public function ChooseUser(): void
    {
        $this->viewComponent('ChooseUser', ['credentials' => $this->authenticationController->GetCredentials()]);
    }

    /**
     * @throws ReflectionException
     */
    public function SelectUser(string $userName): void
    {
        $languages = $this->languageController->Get();
        $this->viewComponent('Connection', ['UserName' => $userName, 'languages' => $languages, 'appVersion' => $this->appVersion]);
    }

    /**
     * @throws ReflectionException
     */
    public function ResetPassword(): void
    {
        $this->view('ResetPassword');
    }

    /**
     * @throws Exception
     */
    public function Connection(ConnectionModel $model): void
    {
        $credential = $this->authenticationController->Login($model);

        if (!$credential)
            throw new Exception();

        $utils = new Utils();
        $utils->SetSession([
            'SessionId' => $credential->It()->SessionId,
            'UserName' => $credential->It()->UserName,
            'ProfileId' => $credential->Profile()->It()->Id,
            'LastName' => $credential->Profile()->It()->LastName,
            'Session' => session_id()
        ]);

        $this->redirectToAction('Index', 'Dashboard');
    }

    /**
     * @throws Exception
     */
    public function Disconnect(): void
    {
        $this->authenticationController->Logout($_SESSION['SessionId']);
        session_destroy();
        $this->redirectToAction('Index');
    }
}