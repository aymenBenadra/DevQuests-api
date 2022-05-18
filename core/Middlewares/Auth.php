<?php

namespace Core\Middlewares;

use Core\Router;
use Core\Helpers\Request;
use App\Models\User;
use Core\Helpers\Response;
use Firebase\JWT\{JWT, Key};
use Exception;

/**
 * Auth class
 * - Handle the request
 * - Check if the user is authenticated
 * - Check if the user is authorized 
 * 
 * @package    Core
 * @author     Mohammed-Aymen Benadra
 * 
 */
class Auth
{
    /**
     * Setup headers for JSON response
     * 
     * @return void
     */
    public function __construct()
    {
        Response::headers();
    }

    /**
     * Handle Authentication using session
     * 
     * @param  mixed $role
     * @return void
     */
    public function handle($role)
    {
        $jwtToken = Request::authorization() ?? null;

        switch ($role) {
            case 'guest':
                if (!$jwtToken) {
                    return;
                }
                break;

            case 'client':
                if ($this->checkJWT($jwtToken)) {
                    return;
                }
                break;

            case 'admin':
                if ($this->checkJWT($jwtToken, 'admin')) {
                    return;
                }
                break;

            default:
                break;
        }

        // Redirect to login page if guest and to home page if user
        if (!$jwtToken) {
            Router::abort(401, [
                'message' => 'Unauthorized: You must be logged in'
            ]);
        } else {
            Router::abort(403, [
                'message' => 'Unauthorized: You\'re not allowed to access this page'
            ]);
        }
    }

    /**
     * Checks if JWT is valid and returns true if it does
     * 
     * @param  string $jwt
     * @param  string $role
     * @return boolean
     */
    public function checkJWT($jwt, $role = null)
    {
        $refreshToken = Request::refreshToken();

        try {
            if (!$refreshToken) {
                throw new Exception('Invalid Refresh Token');
            }
            if (!$jwt) {
                throw new Exception('Invalid Access Token');
            }

            $accessToken = JWT::decode($jwt, new Key($_ENV['JWT_SECRET_KEY'], $_ENV['JWT_ALGORITHM']));
            $refreshToken = JWT::decode($refreshToken, new Key($_ENV['JWT_SECRET_KEY'], $_ENV['JWT_ALGORITHM']));

            // Check if access token is valid
            if ($accessToken->sub !== $refreshToken->sub) {
                throw new Exception('Invalid Access Token');
            }


            // Check if User exists
            $user = (new User())->getBy('username', $accessToken->sub);
            if (!$user) {
                throw new Exception('User not found');
            }

            // Check if user is authorized
            if ($role === 'admin' && $user->is_admin !== 1) {
                throw new Exception('You\'re not allowed to access this page');
            }

            return true;
        } catch (Exception $e) {
            Router::abort(401, [
                'message' => 'Unauthorized: ' . $e->getMessage()
            ]);
        }
    }
}
