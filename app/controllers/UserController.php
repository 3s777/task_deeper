<?php

namespace App\Controllers;

use App\Models\QueryBuilder;
use Delight\Auth\Auth;
use League\Plates\Engine;
use PDO;
use Tamtamchik\SimpleFlash\Flash;
use App\Validator;
use App\Helpers;

class UserController
{
    private $template;
    private $auth;
    private $flash;
    private $db;
    private $helpers;

    public function __construct(Engine $engine, Auth $auth, Flash $flash, QueryBuilder $db, Helpers $helpers)
    {
        $this->template = $engine;
        $this->auth = $auth;
        $this->flash = $flash;
        $this->db = $db;
        $this->helpers = $helpers;

        if (!$auth->isLoggedIn()) {
            $this->helpers->redirect_to("/login_page");
        }
    }

    /**
     * Cтраница для вывода всех пользователей
     */
    public function index() {
        $users = $this->db->selectAll('users');

        foreach ($users as $key => $user) {
            $path = $this->get_avatar_path($user['avatar']);
            $status_icon = $this->set_status_icon($user['online_status']);
            $users[$key]['avatar_path'] = $path;
            $users[$key]['status_icon'] = $status_icon;
        }

        echo $this->template->render('users', ['users' => $users, 'auth' => $this->auth]);
    }


    /**
     * Cтраница для вывода пользователя
     * @param $id int
     */
    public function show($id) {
        $user = $this->db->selectOne($id, 'users');
        $path = $this->get_avatar_path($user['avatar']);
        echo $this->template->render('user_profile', ['user' => $user, 'path' => $path]);
    }

    /**
     * Cтраница добавления нового пользователя
     */
    public function create() {
        echo $this->template->render('user_create');
    }

    /**
     * Cохраняем нового пользователя
     */
    public function store() {

        $data_for_validate = [
            ['data' => $_POST['password'], 'name' => 'Пароль', 'rules' => 'required'],
            ['data' => $_POST['email'], 'name' => 'Эмейл', 'rules' => 'required|email'],
            ['data' => $_POST['username'], 'name' => 'Имя', 'rules' => 'required'],
        ];

        $result = Validator::validate($data_for_validate);

        if(!empty($result)) {
            $this->flash->error($result);
            $this->helpers->redirect_to("/user_create");
        }

        $email = Validator::clean($_POST['email']);
        $password = Validator::clean($_POST['password']);
        $username = Validator::clean($_POST['username']);
        $phone = Validator::clean($_POST['phone']);
        $address = Validator::clean($_POST['address']);
        $job = Validator::clean($_POST['job']);
        $vk = Validator::clean($_POST['vk']);
        $telegram = Validator::clean($_POST['telegram']);
        $instagram = Validator::clean($_POST['instagram']);
        $online_status = Validator::clean($_POST['online_status']);

        try {
            $userId = $this->auth->admin()->createUser($email, $password, $username);
            $avatar = $this->upload_user_avatar($userId, $_FILES['avatar']);

            $this->db->update($userId, [
                'phone'=>$phone,
                'address'=>$address,
                'job'=>$job,
                'avatar'=>$avatar,
                'vk'=>$vk,
                'telegram'=>$telegram,
                'instagram'=>$instagram,
                'online_status'=>$online_status,
            ], 'users');

            $this->flash->success('Новый пользователь успешно добавлен');
            $this->helpers->redirect_to("/");
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            $this->flash->error('Неправильный Email');
            $this->helpers->redirect_to("/user_create");
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            $this->flash->error('Неправильный пароль');
            $this->helpers->redirect_to("/user_create");
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            $this->flash->error('Пользователь уже существует');
            $this->helpers->redirect_to("/user_create");
        }
    }

    /**
     * Cтраница редактирования пользователя
     * @param $id int
     */
    public function edit($id) {
        $this->check_role($id);
        $user = $this->db->selectOne($id, 'users');
        echo $this->template->render('user_edit', ['user' => $user]);
    }

    /**
     * Cохраняем нового пользователя
     * @param $id int
     */
    public function update($id) {
        $this->check_role($id);

        $data_for_validate = [
            ['data' => $_POST['username'], 'name' => 'Имя', 'rules' => 'required'],
        ];

        $result = Validator::validate($data_for_validate);

        if(!empty($result)) {
            $this->flash->error($result);
            $this->helpers->redirect_to("/");
        }

        $username = Validator::clean($_POST['username']);
        $phone = Validator::clean($_POST['phone']);
        $address = Validator::clean($_POST['address']);
        $job = Validator::clean($_POST['job']);

        $this->db->update($id, [
            'phone'=>$phone,
            'address'=>$address,
            'job'=>$job,
            'username'=>$username
        ], 'users');

        $this->flash->success('Пользователь успешно отредактирован');
        $this->helpers->redirect_to("/");

    }

    /**
     * Редактируем эмейл и пароль
     * @param $id int
     */
    public function edit_security($id) {
        $this->check_role($id);
        $user = $this->db->selectOne($id, 'users');
        echo $this->template->render('user_security', ['user' => $user]);
    }

    /**
     * Cохраняем эмейл и пароль
     * @param $id int
     */
    public function update_security($id) {

        $this->check_role($id);

        $data_for_validate = [
            ['data' => $_POST['email'], 'name' => 'Email', 'rules' => 'required|email'],
        ];

        $result = Validator::validate($data_for_validate);

        if(!empty($result)) {
            $this->flash->error($result);
            $this->helpers->redirect_to("/user_edit_security/$id");
        }

        $email = Validator::clean($_POST['email']);
        $password = Validator::clean($_POST['password']);
        $confirm_password = Validator::clean($_POST['confirm_password']);

        $checking_email_user = $this->db->selectOneByEmail($email, 'users');

        if(!empty($checking_email_user) && $checking_email_user['id'] != $id) {
            $this->flash->error('Этот email уже занят');
            $this->helpers->redirect_to("/user_edit_security/$id");
        }

        if(!empty($password)) {
            if($password == $confirm_password) {
                $this->db->update($id, [
                    'email'=>$email,
                    'password'=>$password,
                ], 'users');
            } else {
                $this->flash->error('Введеные пароли не совпадают');
                $this->helpers->redirect_to("/user_edit_security/$id");
            }
        } else {
            $this->db->update($id, [
                'email'=>$email,
            ], 'users');
        }

        $this->flash->success('Пользователь успешно отредактирован');
        $this->helpers->redirect_to("/");
    }

    /**
     * Редактируем статус пользователя
     * @param $id int
     */
    public function edit_status($id) {
        $this->check_role($id);
        $user = $this->db->selectOne($id, 'users');
        echo $this->template->render('user_status', ['user' => $user]);
    }

    /**
     * Cохраняем статус пользователя
     * @param $id int
     */
    public function update_status($id) {
        $this->check_role($id);

        $online_status = Validator::clean($_POST['online_status']);

        $this->db->update($id, [
                'online_status'=>$online_status,
            ], 'users');

        $this->flash->success('Статус успешно установлен');
        $this->helpers->redirect_to("/");
    }

    /**
     * Редактируем аватар пользователя
     * @param $id int
     */
    public function edit_avatar($id) {
        $this->check_role($id);
        $user = $this->db->selectOne($id, 'users');
        $path = $this->get_avatar_path($user['avatar']);
        echo $this->template->render('user_avatar', ['user' => $user, 'path' => $path]);
    }


    /**
     * Сохраняем аватар пользователя
     * @param $id int
     */
    public function update_avatar($id) {
        $this->check_role($id);
        $avatar = $this->upload_user_avatar($id, $_FILES['avatar']);

        $this->db->update($id, [
            'avatar'=>$avatar,
        ], 'users');

        $this->flash->success('Аватар успешно установлен');
        $this->helpers->redirect_to("/");
    }

    /**
     * Удаляем пользователя
     * @param $id int
     */
    public function user_delete($id) {
        $this->check_role($id);
        $this->check_and_delete_user_avatar($id);
        $this->db->delete($id, 'users');
        $this->flash->success('Пользователь удален');
        $this->helpers->redirect_to("/");
    }

    /**
     * Проверяем и удаляем аватар пользователя если он установлен
     * @param $id int
     */
    public function check_and_delete_user_avatar($id) {
        $user = $this->db->selectOne($id, 'users');

        if(!empty($user['avatar'])) {
            unlink("assets/img/demo/avatars/".$user['avatar']);
        }
    }

    /**
     * Загружаем аватар
     * @param $id int
     * @param $avatar string
     * @return string
     */
    public function upload_user_avatar($id, $avatar) {
        $image = $avatar['name'];
        if(!empty($image)) {
            $this->check_and_delete_user_avatar($id);

            $image_tmp_name = $avatar['tmp_name'];
            $extension = pathinfo($image, PATHINFO_EXTENSION);
            $uploaded_file_name = uniqid();
            $full_uploaded_file_name = $uploaded_file_name.".".$extension;
            move_uploaded_file($image_tmp_name, "assets/img/demo/avatars/".$full_uploaded_file_name);

            return $full_uploaded_file_name;
        }
    }

    /**
     * Получаем полный путь к аватару пользователя
     * @param $avatar string
     * @return string
     */
    public function get_avatar_path($avatar) {
        $avatar_path = '/assets/img/demo/avatars/';
        if(!empty($avatar)) {
            $path = $avatar_path.$avatar;
        } else {
            $path = $avatar_path.'avatar-m.png';
        }
        return $path;
    }

    /**
     * Устанавливаем статус иконки
     * @param $online_status string
     * @return string
     */
    public function set_status_icon($online_status)
    {
        switch ($online_status) {
            case 'online':
                $icon = 'status-success';
                break;
            case 'busy':
                $icon = 'status-danger';
                break;
            default:
                $icon = 'status-secondary';
                break;
        }
        return $icon;
    }

    /**
     * Проверяем роль и доступы
     * @param $id int
     */
    public function check_role($id)
    {
        if(!$this->auth->hasRole(\Delight\Auth\Role::ADMIN) && $this->auth->getUserId() != $id) {
            $this->flash->error('У вас нет прав редактировать этого пользователя');
            $this->helpers->redirect_to("/");
        }
    }

    /**
     * Убираем пользователю авторизацию
     */
    public function logout() {
        $this->auth->logOut();
        echo $this->template->render('page_login');
    }
}