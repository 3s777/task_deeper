<?php
namespace App\Controllers;
use League\Plates\Engine;
use Dotenv\Dotenv;
use PDO;
use Delight\Auth\Auth;
use SimpleMail;
use Tamtamchik\SimpleFlash\Flash;
use App\Helpers;

class AuthController
{
    private $template;
    private $pdo;
    private $auth;
    private $flash;
    private $helpers;

    public function __construct(PDO $pdo, Engine $engine, Auth $auth, Flash $flash, Helpers $helpers)
    {
        $this->template = $engine;
        $this->pdo = $pdo;
        $this->auth = $auth;
        $this->flash = $flash;
        $this->helpers = $helpers;

        if ($auth->isLoggedIn()) {
            $this->helpers->redirect_to('/');
        }
    }

    /**
     * Страница авторизации
     */
    public function login_page() {
        echo $this->template->render('page_login');
    }

    /**
     * Авторизуем пользователя
     */
    public function login() {
        try {
            $this->auth->login($_POST['email'], $_POST['password']);
            $this->helpers->redirect_to('/');
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            $this->flash->error('Неправильный эмейл');
            $this->helpers->redirect_to('/login_page');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            $this->flash->error('Неправильный пароль');
            $this->helpers->redirect_to('/login_page');
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            $this->flash->error('Email не подтвержден');
            $this->helpers->redirect_to('/login_page');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            $this->flash->error('Слишком много попыток');
            $this->helpers->redirect_to('/login_page');
        }
    }

    /**
     * Страница регистрации
     */
    public function register_page() {
        echo $this->template->render('page_register');
    }

    /**
     * Регистрируем пользователя
     */
    public function register() {
        try {
            $userId = $this->auth->register($_POST['email'], $_POST['password'],'', function ($selector, $token) {
                SimpleMail::make()
                    ->setTo($_POST['email'], 'User')
                    ->setFrom('tasklevel3.loc', "Admin")
                    ->setSubject("Подтверждение регистрации")
                    ->setMessage("Для того чтобы подтвердить аккаунт, перейдите, пожалуйста по <a href='http://tasklevel3.loc/verify_email?selector=" . \urlencode($selector) . "&token=" . \urlencode($token) ."'>ссылке</a>")
                    ->send();
                $this->flash->error('Мы вас зарегистрировали, на вашу почту было отправлено письмо для подтверждения Email. Перейдите по ссылке из письма');
                $this->helpers->redirect_to('/login_page');
            });

        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            $this->flash->error('Неправильный эмейл');
            $this->helpers->redirect_to('/register_page');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            $this->flash->error('Неправильный пароль');
            $this->helpers->redirect_to('/register_page');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            $this->flash->error('Пользователь уже существует');
            $this->helpers->redirect_to('/register_page');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            $this->flash->error('Слишком много попыток');
            $this->helpers->redirect_to('/register_page');
        }
    }

    /**
     * Верифицируем аккаунт
     */
    public function verification() {
        try {
            $this->auth->confirmEmail($_GET['selector'], $_GET['token']);
            $this->flash->success('Email успешно подтвержден, можете авторизоваться');
            $this->helpers->redirect_to('/login_page');
        }
        catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            $this->flash->error('Неверный токен');
            $this->helpers->redirect_to('/login_page');
        }
        catch (\Delight\Auth\TokenExpiredException $e) {
            $this->flash->error('Срок действия ссылки истек');
            $this->helpers->redirect_to('/login_page');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            $this->flash->error('Email уже подтвержден');
            $this->helpers->redirect_to('/login_page');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            $this->flash->error('Слишком много запросов');
            $this->helpers->redirect_to('/login_page');
        }
    }
}