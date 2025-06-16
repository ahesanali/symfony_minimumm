<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\HelloService;
use App\Utils\Validator;
use App\Utils\Auth\AuthManager;
use Psr\Log\LoggerInterface;
use App\Service\AuthHelperService;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class ConferenceController extends BaseController
{
    private HelloService $helloService;
    private AuthManager $authManager;

    // If needed include AuthHelperService for authorization checks in controllers and views
    public function __construct(private LoggerInterface $logger,HelloService $helloService, AuthManager $authManager,
    AuthHelperService $authHelper)
    {
        parent::__construct($authHelper);
        // Initialize services
        $this->helloService = $helloService;
        $this->authManager = $authManager;
    }


    #[Route('/conference', name: 'app_conference', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        

        $name = $request->query->get('name', 'Ahesan');
        $this->logger->info('Conference route', ['user_id' => $name]);
        return $this->json([
            'message' => 'Welcome to your new controller! ' . $this->helloService->sayHello(),
            'name' => $name,
            'path' => 'src/Controller/ConferenceController.php',
        ]);
    }

    #[Route('/home', name: 'app_home', methods: ['GET'])]
    public function home(): JsonResponse
    {
        return $this->json([
            'message' => 'New Home Route!',
            'path' => 'src/Controller/ConferenceController.php',
        ]);
    }

    #[Route('/', name: 'app_test_view',     methods: ['GET'])]
    public function testView(): Response
    {
        $content = $this->template('users/user_list', ['message' => 'Hello']);
        return new Response($content);
    }

    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        $this->logger->info('Login  route');
        $content = $this->template('users/login');
        return new Response($content);
    }

    #[Route('/authenticate', name: 'app_authenticate', methods: ['POST'])]
    public function authenticate(Request $request): JsonResponse
    {
        $rules = [
            'user_id' => ['required', 'min_length:5'],
            'password' => ['required', 'min_length:6'],
        ];

        $this->logger->info('Authenti  route',['posted data' => $request->request->all()   ]);
        $validator_instance = Validator::makeValidator($request->request->all(), $rules, true);
        $jsonresponse = $validator_instance->sendValidationErrorsIfAny("Please correct below errors");
        if(!is_null($jsonresponse)) {
            return $jsonresponse;
        }

        $user_id = $request->request->get('user_id');
        $password = $request->request->get('password');

      
        $authenticated = $this->authManager->performAuth($user_id, $password);
        if ($authenticated) {
            return $this->json(
                [
                    'responseCode' => 200,
                    'responseMessage' => "Successfully logged in",
                    'responseContent' => ["authenticated" => $authenticated]
                ],
                200
            );
        }
        return  $this->json(
                [
                    'responseCode' => 500,
                    'responseMessage' => "Either user id or password is incorrect.",
                    'responseContent' => []
                ],
                500
            );
    }

    #[Route('/dashboard', name: 'app_dashboard',methods: ['GET'])]
    public function dashboard(Request $request): Response
    {
        // Check if the user is logged in
        if (! $this->authHelper->isUserLoggedIn()) {
            return $this->redirect('login'); // or a custom error page
        }
         $this->addFlash('success', 'Welcome to the dashboard!');
         
         
        // User is logged in, proceed with the logic
        $loggedInUser = $this->authHelper->getLoggedInUser();
        $content = $this->template('users/dashboard',compact('loggedInUser'));
       

        return new Response($content);
    }


    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): Response
    {
        $this->logger->info('Logging out from the application::  route');
        $this->authManager->logout();

         return $this->redirect('login'); // or a custom error page
    }
}
