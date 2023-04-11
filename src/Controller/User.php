<?php
namespace App;

require_once "src/autoload.php";
require_once "src/db.php";
require_once "src/function.php";

use App\SendResetPasswordMail as SendResetMail;

class User
{
    /**
     * getUserArray
     *
     * @return void
     */
    public static function getUserArray()
    {
        $sql = [];
        $sql['sql'] = "SELECT * FROM `Users`";
        $usersDataArray = Select($sql['sql']);
        echo json_encode($usersDataArray);
    }
    
    /**
     * getUser
     * getting user data
     * @param  integer $id
     * @return void
     */
    public static function getUser(int $id)
    {
        if (isset($id)) {
            $sql = [];
            $sql['sql'] = "SELECT * FROM `Users` WHERE Users.id = :id";
            $sql['param'] = [
                'id' => htmlspecialchars(trim($id)),
            ];
            $userData = Select($sql['sql'], $sql['param']);

            if (empty($userData)) {
                echo $message = "you are not registered";
            } else {
                echo json_encode($userData);
            }
        } else {
            echo $message = "id not found";
        }
    }
    
    /**
     * addUser
     * adding user to data base
     * @return void
     */
    public static function addUser()
    {
        $data = $_POST;
        if (isset($data['email']) && isset($data['password'])) {
            $sql = [];
            if (isset($data['first_name'])) {
                $first_name = $data['first_name'];
            } else {
                $first_name = null;
            }
            $role = "ROLE_USER";

            $sql['sql'] = "SELECT * FROM `Users` WHERE Users.email = :email";
            $sql['param'] = [
                'email' => htmlspecialchars(trim($data['email'])),
            ];
            $userData = Select($sql['sql'], $sql['param']);

            if (empty($userData)) {
                $sql['sql'] = "INSERT INTO `Users` (Users.email, Users.password, Users.role, Users.first_name) VALUES (:email, :passwordUser, :roleUser, :first_name)";
                $sql['param'] = [
                    'email' => htmlspecialchars(trim($data['email'])),
                    'passwordUser' => password_hash($data['password'], PASSWORD_DEFAULT),
                    'roleUser' => $role,
                    'first_name' => htmlspecialchars(trim($first_name)),
                ];
                Query($sql['sql'], $sql['param']);

                echo $message = "success";
            } else {
                echo $message = "such a user already exists";
            }
        } else {
            echo $message = "email and password not found";
        }
    }
    
    /**
     * changeUser
     * changing user data from data base
     * @return void
     */
    public static function changeUser()
    {
        $str = (file_get_contents('php://input'));

        if (!empty($str)) {
            $data = parseDataRequest($str);
        }

        if (isset($data['email']) && isset($data['password'])) {
            $sql = [];
            if (isset($data['first_name'])) {
                $first_name = $data['first_name'];
            } else {
                $first_name = null;
            }
            $role = "ROLE_USER";

            $sql['sql'] = "SELECT * FROM `Users` WHERE Users.email = :email";
            $sql['param'] = [
                'email' => htmlspecialchars(trim($data['email'])),
            ];
            $userData = Select($sql['sql'], $sql['param']);

            if (! empty($userData)) {
                $sql['sql'] = "UPDATE `Users` SET Users.email =:email, Users.password =:passwordUser, Users.role =:roleUser, Users.first_name =:first_name WHERE Users.email =:email";
                $sql['param'] = [
                    'email' => htmlspecialchars(trim($data['email'])),
                    'passwordUser' => password_hash($data['password'], PASSWORD_DEFAULT),
                    'roleUser' => $role,
                    'first_name' => htmlspecialchars(trim($first_name)),
                ];
                Query($sql['sql'], $sql['param']);

                echo $message = "success";
            } else {
                echo $message = "you are not registered";
            }
        } else {
            echo $message = "email and password not found";
        }
    }
    
    /**
     * deleteUser
     * deleting user from data base
     * @param  integer $id
     * @return void
     */
    public static function deleteUser(int $id)
    {
        if (isset($id)) {
            $sql = [];
            $sql['sql'] = "SELECT * FROM `Users` WHERE Users.id = :id";
            $sql['param'] = [
                'id' => htmlspecialchars(trim($id)),
            ];
            $userData = Select($sql['sql'], $sql['param']);

            if (!empty($userData)) {
                $sql['sql'] = "DELETE FROM `Users` WHERE Users.id = :id";
                $sql['param'] = [
                    'id' => $id,
                ];
                Query($sql['sql'], $sql['param']);
                echo $message = "user was deleted";
            } else {
                echo $message = "incorrect id";
            }
        } else {
            echo $message = "id not found";
        }
    }
    
    /**
     * login
     *
     * @param  mixed $email
     * @param  mixed $password
     * @return void
     */
    public static function login(string $email, string $password)
    {
        if (isset($email) && isset($password)) {
            $sql = [];
            $sql['sql'] = "SELECT * FROM `Users` WHERE Users.email=:email";
            $sql['param'] = [
                'email' => htmlspecialchars(trim($email)),
            ];
            $userData = SelectRow($sql['sql'], $sql['param']);
            if (!empty($userData)) {
                if (password_verify($password, $userData[0]['password'])) {
                    $lifetime = 3600;
                    setcookie('id_auth_user', session_id(), time() + $lifetime);
                    $_SESSION['ROLE'] = $userData[0]['role'];
                    $_SESSION['NAME'] = $userData[0]['first_name'];
                    $_SESSION['EMAIL'] = $userData[0]['email'];
                    header('Location: /admin/user');
                } else {
                    echo $message = "Your email and password combination is invalid";
                }
            } else {
                echo $message = "Your email and password combination is invalid";
            }
        } else {
            echo $message = "email and password not found";
        }
    }
    
    /**
     * loguot user
     *
     * @return void
     */
    public static function logout()
    {
        session_destroy();
        setcookie(session_name(), session_id());
        setcookie('id_auth_user', '', time());
    }
    
    /**
     * resetPassword
     *
     * @param  mixed $email
     * @param  mixed $first_name
     * @return void
     */
    public static function resetPassword(string $email, string $first_name)
    {
        if (isset($email) && isset($first_name)) {
            $sql = [];
            $sql['sql'] = "SELECT * FROM `Users` WHERE Users.email=:email AND Users.first_name =:first_name";
            $sql['param'] = [
                'email' => htmlspecialchars(trim($email)),
                'first_name' => htmlspecialchars(trim($first_name)),
            ];
            $userData = SelectRow($sql['sql'], $sql['param']);
            if (!empty($userData)) {
                $sendMail = SendResetMail::sendResetPasswordMail($email, $first_name);
                echo $sendMail;
            }
        } else {
            echo $message = "email and first name not found";
        }
    }

    public static function changePassword(string $email, string $first_name)
    {
        if (isset($email) && isset($first_name)) {
            $sql = [];
            $sql['sql'] = "SELECT * FROM `Users` WHERE Users.email=:email AND Users.first_name =:first_name";
            $sql['param'] = [
                'email' => htmlspecialchars(trim($email)),
                'first_name' => htmlspecialchars(trim($first_name)),
            ];
            $userData = SelectRow($sql['sql'], $sql['param']);

            
            if (!empty($userData)) {
                $sql['sql'] = "UPDATE `Users` SET Users.password =:passwordUser WHERE Users.email =:email AND Users.first_name =:first_name";
                $sql['param'] = [
                    'email' => htmlspecialchars(trim($email)),
                    'passwordUser' => password_hash('123456', PASSWORD_DEFAULT),
                    'first_name' => htmlspecialchars(trim($first_name)),
                ];
                Query($sql['sql'], $sql['param']);

                echo $message = "success";
            }
        } else {
            echo $message = "email and first name not found";
        }
    }
}
