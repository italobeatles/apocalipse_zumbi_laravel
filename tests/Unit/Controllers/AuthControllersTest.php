<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthControllersTest extends TestCase {

    #[Test]
    public function login_controller_stub_retorna_501() {
        $controller = new LoginController();
        $response = $controller->unavailable();

        $this->assertSame('/home', $this->getProtectedProperty($controller, 'redirectTo'));
        $this->assertSame(Response::HTTP_NOT_IMPLEMENTED, $response->getStatusCode());
    }

    #[Test]
    public function register_controller_stub_retorna_501() {
        $controller = new RegisterController();
        $response = $controller->unavailable();

        $this->assertSame('/home', $this->getProtectedProperty($controller, 'redirectTo'));
        $this->assertSame(Response::HTTP_NOT_IMPLEMENTED, $response->getStatusCode());
    }

    #[Test]
    public function forgot_password_controller_stub_retorna_501() {
        $response = (new ForgotPasswordController())->unavailable();

        $this->assertSame(Response::HTTP_NOT_IMPLEMENTED, $response->getStatusCode());
    }

    #[Test]
    public function reset_password_controller_stub_retorna_501() {
        $controller = new ResetPasswordController();
        $response = $controller->unavailable();

        $this->assertSame('/home', $this->getProtectedProperty($controller, 'redirectTo'));
        $this->assertSame(Response::HTTP_NOT_IMPLEMENTED, $response->getStatusCode());
    }

    private function getProtectedProperty(object $object, string $name): mixed {
        $ref = new \ReflectionProperty($object, $name);
        $ref->setAccessible(true);
        return $ref->getValue($object);
    }
}

