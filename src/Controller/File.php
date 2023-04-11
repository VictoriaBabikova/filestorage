<?php
namespace App;

require_once "src/autoload.php";
require_once "src/db.php";
require_once "src/function.php";

class File
{
    /**
     * getFilesArray get a list of files of the logged-in user
     *
     * @return void
     */
    public static function getFilesArray()
    {
        if (isset($_SESSION['ROLE'])) {
            $sql = [];
                $sql['sql'] = "SELECT Users.id FROM `Users` WHERE Users.email=:email AND Users.first_name =:first_name";
                $sql['param'] = [
                    'email' => htmlspecialchars($_SESSION['EMAIL']),
                    'first_name' => htmlspecialchars($_SESSION['NAME']),
                ];
                $userData = SelectRow($sql['sql'], $sql['param']);

                if (!empty($userData)) {
                    $sql['sql'] = "SELECT Files.name_file, Files.path_file FROM `Files` WHERE Files.user_id = :user_id";
                    $sql['param'] = [
                        'user_id' => $userData[0]['id'],
                    ];
                    $fileData = SelectRow($sql['sql'], $sql['param']);

                    echo json_encode($fileData);
                } else {
                    echo $message = "there are no uploaded files";
                }
        } else {
            echo $message = "you need login-in";
        }
    }
    
    /**
     * addFile adding a file by a logged-in user
     *
     * @return void
     */
    public static function addFile()
    {
        if (isset($_SESSION['ROLE'])) {
            $data = array_merge($_POST, $_FILES); /**params: name_dir and file(uploaded file) */
            if (isset($data['name_dir']) && isset($data['file'])) {
                $sql = [];
                $sql['sql'] = "SELECT Users.id FROM `Users` WHERE Users.email=:email AND Users.first_name =:first_name";
                $sql['param'] = [
                    'email' => htmlspecialchars($_SESSION['EMAIL']),
                    'first_name' => htmlspecialchars($_SESSION['NAME']),
                ];
                $userData = SelectRow($sql['sql'], $sql['param']);

                if (!empty($userData)) {
                    $sql['sql'] = "SELECT * FROM `Directories` WHERE Directories.name_dir = :name_dir AND Directories.user_id = :user_id";
                    $sql['param'] = [
                        'name_dir' => htmlspecialchars(trim($data['name_dir'])),
                        'user_id' => $userData[0]['id'],
                    ];
                    $dirData = SelectRow($sql['sql'], $sql['param']);

                    if (!empty($dirData)) {
                        $pathFile = $dirData[0]['path_dir'] . "/" . $data['file']['name'];

                        if (file_exists($pathFile)) {
                            echo $message = "a file with that name already exists";
                        } else {
                            move_uploaded_file($data['file']['tmp_name'], $pathFile);
                            $userAccess = $userData[0]['id'];
                            $sql['sql'] = "INSERT INTO `Files` (Files.name_file, Files.path_file, Files.user_id, Files.date_create, Files.list_user_access) VALUES (:name_file, :path_file, :user_id, :date_create, JSON_ARRAY('$userAccess'))";
                            $sql['param'] = [
                                'name_file' => htmlspecialchars(trim($data['file']['name'])),
                                'path_file' => $pathFile,
                                'user_id' => $userData[0]['id'],
                                'date_create' => date("Y-m-d H:i:s"),
                            ];
                            Query($sql['sql'], $sql['param']);

                            echo $message = "the file was uploaded successfully";
                        }
                    }
                }
            } else {
                echo $message = "the directory or file name does not exist";
            }
        } else {
            echo $message = "you need login-in";
        }
    }
    
    /**
     * changeFile rename file or move a file to another directory by a logged-in user
     *
     * @return void
     */
    public static function changeFile()
    {
        if (isset($_SESSION['ROLE'])) {
            $str = (file_get_contents('php://input'));
            if (!empty($str)) {
                $data = parseDataRequest($str);
            }
    
            $sql = [];
            $sql['sql'] = "SELECT Users.id FROM `Users` WHERE Users.email=:email AND Users.first_name =:first_name";
            $sql['param'] = [
                'email' => htmlspecialchars($_SESSION['EMAIL']),
                'first_name' => htmlspecialchars($_SESSION['NAME']),
            ];
            $userData = SelectRow($sql['sql'], $sql['param']);
                
            if (!empty($userData)) {
                if (isset($data['name_file'])) {
                    $sql['sql'] = "SELECT  Files.name_file, Files.path_file FROM `Files` WHERE Files.name_file = :old_name_file AND Files.user_id = :user_id";
                    $sql['param'] = [
                        'old_name_file' => $data['name_file'],
                        'user_id' => $userData[0]['id'],
                    ];
                    $fileData = SelectRow($sql['sql'], $sql['param']);

                    if (isset($data['new_name_file']) && (!empty($fileData))) { /** rename file */
                        if (file_exists($fileData[0]['path_file'])) {
                            $old_path = $fileData[0]['path_file'];
                            $arrPath = explode($fileData[0]['name_file'], $old_path);
                            $new_path = $arrPath[0] . $data['new_name_file'];
                            rename($old_path, $new_path);

                            $sql['sql'] = "UPDATE `Files` SET Files.name_file = :new_name_file, Files.path_file = :path_file, Files.date_create = :date_create WHERE Files.name_file =:name_file";
                            $sql['param'] = [
                                'new_name_file' => htmlspecialchars(trim($data['new_name_file'])),
                                'path_file' => $new_path,
                                'date_create' => date("Y-m-d H:i:s"),
                                'name_file' => htmlspecialchars(trim($data['name_file'])),
                            ];
                            Query($sql['sql'], $sql['param']);

                            echo $message = "the file has been renamed successfully";
                        } else {
                            echo $message = "failed to rename a file";
                        }
                    }

                    if (isset($data['new_dir']) && (!empty($fileData))) { /** move another directory */
                        $path = $_SERVER['DOCUMENT_ROOT'] . "/storage" ."/". $data['new_dir'];
                        $new_path = $path . "/" . $data['name_file'];
                        
                        if (is_dir($path)) {
                            if (copy($fileData[0]['path_file'], $new_path)) {
                                $sql['sql'] = "UPDATE `Files` SET Files.path_file = :path_file, Files.date_create = :date_create WHERE Files.name_file =:name_file";
                                $sql['param'] = [
                                'path_file' => $new_path,
                                'date_create' => date("Y-m-d H:i:s"),
                                'name_file' => htmlspecialchars(trim($data['name_file'])),
                                ];
                                Query($sql['sql'], $sql['param']);
                                unlink($fileData[0]['path_file']);

                                echo $message = "the file has been moved successfully";
                            } else {
                                echo $message = "failed to move a file";
                            }
                        } else {
                            echo $message = "such a directory does not exist";
                        }
                    }
                }
            }
        } else {
            echo $message = "you need login-in";
        }
    }
    
    /**
     * getFileInfo by a logged-in user
     *
     * @param  mixed $id_file
     * @return void
     */
    public static function getFileInfo(int $id_file)
    {
        if (isset($_SESSION['ROLE'])) {
            if (isset($id_file)) {
                $sql['sql'] = "SELECT  * FROM `Files` WHERE Files.id = :id_file";
                $sql['param'] = [
                    'id_file' => htmlspecialchars(trim($id_file)),
                ];
                $fileData = SelectRow($sql['sql'], $sql['param']);
                $arrSize = [
                    'size file' => filesize($fileData[0]['path_file'])
                ];
                $fileData[0] = array_merge($arrSize, $fileData[0]);
                echo json_encode($fileData[0]);
            }
        } else {
            echo $message = "you need login-in";
        }
    }

    public static function deleteFile(int $id_file)
    {
        if (isset($_SESSION['ROLE'])) {
            if (isset($id_file)) {
                $sql = [];
                $sql['sql'] = "SELECT Users.id FROM `Users` WHERE Users.email=:email AND Users.first_name =:first_name";
                $sql['param'] = [
                    'email' => htmlspecialchars($_SESSION['EMAIL']),
                    'first_name' => htmlspecialchars($_SESSION['NAME']),
                ];
                $userData = SelectRow($sql['sql'], $sql['param']);
                if (!empty($userData)) {
                    $sql['sql'] = "SELECT  * FROM `Files` WHERE Files.id = :id_file";
                    $sql['param'] = [
                        'id_file' => htmlspecialchars(trim($id_file)),
                    ];
                    $fileData = SelectRow($sql['sql'], $sql['param']);

                    if (!empty($fileData)) {
                        unlink($fileData[0]['path_file']);
                        $sql['sql'] = "DELETE FROM `Files` WHERE Files.id = :id AND Files.user_id = :user_id";
                        $sql['param'] = [
                            'id' => $id_file,
                            'user_id' =>$userData[0]['id'],
                        ];
                        Query($sql['sql'], $sql['param']);
                        echo $message = "files was deleted";
                    }
                }
            }
        } else {
            echo $message = "you need login-in";
        }
    }
    
    /**
     * addDirectory create new directory by a logged-in user
     *
     * @return void
     */
    public static function addDirectory()
    {
        if (isset($_SESSION['ROLE'])) {
            $data = $_POST;
            if (isset($data['name_dir'])) {
                $sql = [];
                $sql['sql'] = "SELECT Users.id FROM `Users` WHERE Users.email=:email AND Users.first_name =:first_name";
                $sql['param'] = [
                    'email' => htmlspecialchars($_SESSION['EMAIL']),
                    'first_name' => htmlspecialchars($_SESSION['NAME']),
                ];
                $userData = SelectRow($sql['sql'], $sql['param']);

                if (!empty($userData)) {
                    $path = $_SERVER['DOCUMENT_ROOT'] . "/storage" ."/". $data['name_dir'];
                    if (is_dir($path)) {
                        echo $message = "this directory already exists";
                    } else {
                        $old = umask(0);
                        mkdir($path, 0777);
                        umask($old);
                        if (is_dir($path)) {
                            $sql['sql'] = "INSERT INTO `Directories` (Directories.name_dir, Directories.path_dir, Directories.user_id, Directories.date_create_dir) VALUES (:name_dir, :path_dir, :user_id, :date_create_dir)";
                            $sql['param'] = [
                                'name_dir' => htmlspecialchars(trim($data['name_dir'])),
                                'path_dir' => $path,
                                'user_id' => $userData[0]['id'],
                                'date_create_dir' => date("Y-m-d H:i:s"),
                            ];
                            Query($sql['sql'], $sql['param']);
                            echo $message = "the directory was created successfully";
                        } else {
                            echo $message = "failed to create a directory";
                        }
                    }
                } else {
                    echo $message = "failed to create a directory";
                }
            } else {
                echo $message = "failed to create a directory";
            }
        } else {
            echo $message = "you need login-in";
        }
    }
    
    /**
     * changeDirectory rename directory by a logged-in user
     *
     * @return void
     */
    public static function changeDirectory()
    {
        if (isset($_SESSION['ROLE'])) {
            $str = (file_get_contents('php://input'));
            if (!empty($str)) {
                $data = parseDataRequest($str);
            }
            if (isset($data['old_name_dir']) && isset($data['new_name_dir'])) {
                $sql = [];
                $sql['sql'] = "SELECT Directories.name_dir FROM `Directories` WHERE Directories.name_dir =:old_name_dir";
                $sql['param'] = [
                    'old_name_dir' => htmlspecialchars($data['old_name_dir']),
                ];
                $userData = SelectRow($sql['sql'], $sql['param']);

                if (!empty($userData[0]['name_dir'])) {
                    $old_path = $path = $_SERVER['DOCUMENT_ROOT'] . "/storage" ."/". $data['old_name_dir'];
                    $new_path = $path = $_SERVER['DOCUMENT_ROOT'] . "/storage" ."/". $data['new_name_dir'];

                    if (is_dir($old_path)) {
                        rename($old_path, $new_path);
                    }
                    
                    $sql['sql'] = "UPDATE `Directories` SET Directories.name_dir = :new_name_dir, Directories.path_dir = :path_dir, Directories.date_create_dir = :date_create_dir WHERE Directories.name_dir =:old_name_dir";
                    $sql['param'] = [
                        'new_name_dir' => htmlspecialchars(trim($data['new_name_dir'])),
                        'path_dir' => $new_path,
                        'date_create_dir' => date("Y-m-d H:i:s"),
                        'old_name_dir' => htmlspecialchars(trim($data['old_name_dir'])),
                    ];
                    Query($sql['sql'], $sql['param']);

                    echo $message = "the directory has been renamed successfully";
                } else {
                    echo $message = "the directory " . $data['old_name_dir'] . " does not exist";
                }
            }
        } else {
            echo $message = "you need login-in";
        }
    }
    
    /**
     * getDirInfo by a logged-in user
     *
     * @param  mixed $id_dir
     * @return void
     */
    public static function getDirInfo(int $id_dir)
    {
        if (isset($_SESSION['ROLE'])) {
            if (isset($id_dir)) {
                $sql = [];
                $sql['sql'] = "SELECT * FROM `Directories` WHERE Directories.id = :id_dir";
                $sql['param'] = [
                    'id_dir' => htmlspecialchars(trim($id_dir)),
                ];
                $dirData = Select($sql['sql'], $sql['param']);
                if (empty($dirData)) {
                    echo $message = "incorrect id";
                } else {
                    $pattern = $dirData[0]['path_dir'];
                    $sql['sql'] = "SELECT Files.name_file FROM `Files` WHERE (path_file REGEXP '^$pattern')";
                    $fileData = Select($sql['sql']);

                    echo json_encode([$dirData[0]['name_dir'] => $fileData]);
                }
            } else {
                echo $message = "id not found";
            }
        } else {
            echo $message = "you need login-in";
        }
    }
    
    /**
     * deleteDir by a logged-in user
     *
     * @param  mixed $id_dir
     * @return void
     */
    public static function deleteDir(int $id_dir)
    {
        if (isset($_SESSION['ROLE'])) {
            if (isset($id_dir)) {
                $sql = [];
                $sql['sql'] = "SELECT * FROM `Directories` WHERE Directories.id = :id";
                $sql['param'] = [
                    'id' => htmlspecialchars(trim($id_dir)),
                ];
                $userData = Select($sql['sql'], $sql['param']);

                if (!empty($userData)) {
                    $result = deleteDirFunc($userData[0]['path_dir']);
                    if (isset($result)) {
                        if (count(scandir($userData[0]['path_dir'])) == 2) {
                            rmdir($userData[0]['path_dir']);
                        }
                    }
                    
                    $sql['sql'] = "DELETE FROM `Directories` WHERE Directories.id = :id";
                    $sql['param'] = [
                        'id' => $id_dir,
                    ];
                    Query($sql['sql'], $sql['param']);
                    echo $message = "directory was deleted";
                } else {
                    echo $message = "incorrect id";
                }
            } else {
                echo $message = "id not found";
            }
        } else {
            echo $message = "you need login-in";
        }
    }
    
    /**
     * getUserInfo user search by email
     *
     * @param  mixed $email
     * @return void
     */
    public static function getUserInfo(string $email)
    {
        if (isset($_SESSION['ROLE'])) {
            if (isset($email)) {
                $sql = [];
                $sql['sql'] = "SELECT Users.id FROM `Users` WHERE Users.email=:email AND Users.first_name =:first_name";
                $sql['param'] = [
                    'email' => htmlspecialchars($_SESSION['EMAIL']),
                    'first_name' => htmlspecialchars($_SESSION['NAME']),
                ];
                $userData = SelectRow($sql['sql'], $sql['param']);

                if (!empty($userData)) {
                    $sql['sql'] = "SELECT * FROM `Users` WHERE Users.email=:email";
                    $sql['param'] = [
                        'email' => htmlspecialchars($email),
                    ];
                    $userDataInfo = SelectRow($sql['sql'], $sql['param']);

                    if (!empty($userDataInfo)) {
                        echo json_encode($userDataInfo);
                    } else {
                        echo $message = "the user with this email address does not exist";
                    }
                }
            } else {
                echo $message = "email not found";
            }
        } else {
            echo $message = "you need login-in";
        }
    }
    
    /**
     * getUserList get a list of users who have access to the file
     *
     * @param  mixed $id_file
     * @return void
     */
    public static function getUserList(int $id_file)
    {
        if (isset($_SESSION['ROLE'])) {
            if (isset($id_file)) {
                $sql = [];
                $sql['sql'] = "SELECT Users.id FROM `Users` WHERE Users.email=:email AND Users.first_name =:first_name";
                $sql['param'] = [
                    'email' => htmlspecialchars($_SESSION['EMAIL']),
                    'first_name' => htmlspecialchars($_SESSION['NAME']),
                ];
                $userData = SelectRow($sql['sql'], $sql['param']);

                if (!empty($userData)) {
                    $sql['sql'] = "SELECT Files.list_user_access FROM `Files` WHERE Files.id = :id_file";
                    $sql['param'] = [
                        'id_file' => htmlspecialchars($id_file),
                    ];
                    $fileData = SelectRow($sql['sql'], $sql['param']);

                    if (!empty($fileData)) {
                        echo $fileData[0];
                    } else {
                        echo $message = "the list of users with access is empty";
                    }
                }
            } else {
                echo $message = "id not found";
            }
        } else {
            echo $message = "you need login-in";
        }
    }
    
    /**
     * setAccessUser set access to user by a logged-in user
     *
     * @return void
     */
    public static function setAccessUser()
    {
        if (isset($_SESSION['ROLE'])) {
            $str = (file_get_contents('php://input'));
            if (!empty($str)) {
                $data = parseDataRequest($str);
            }
            
            if (isset($data['id_file']) && isset($data['id'])) {
                $sql = [];
                $sql['sql'] = "SELECT Users.id FROM `Users` WHERE Users.email=:email AND Users.first_name =:first_name";
                $sql['param'] = [
                    'email' => htmlspecialchars($_SESSION['EMAIL']),
                    'first_name' => htmlspecialchars($_SESSION['NAME']),
                ];
                $userData = SelectRow($sql['sql'], $sql['param']);

                if (!empty($userData)) {
                    $sql['sql'] = "SELECT Files.list_user_access FROM `Files` WHERE Files.id = :id_file";
                    $sql['param'] = [
                        'id_file' => htmlspecialchars($data['id_file']),
                    ];
                    $fileData = SelectRow($sql['sql'], $sql['param']);

                    $arrAccessUser = json_decode($fileData[0]['list_user_access']);
                    array_push($arrAccessUser, $data['id']);

                    $sql['sql'] = "UPDATE `Files` SET Files.list_user_access = :list_user_access, Files.date_create = :date_create WHERE Files.id =:id_file";
                    $sql['param'] = [
                        'list_user_access' => json_encode($arrAccessUser),
                        'date_create' => date("Y-m-d H:i:s"),
                        'id_file' => htmlspecialchars($data['id_file']),
                    ];
                    Query($sql['sql'], $sql['param']);
                    echo $message = "file access was successfully added";
                }
            } else {
                echo $message = "id file or id user not found";
            }
        } else {
            echo $message = "you need login-in";
        }
    }
    
    /**
     * deleteAccessUser revoking user access by a logged-in user
     *
     * @param  mixed $id_file
     * @param  mixed $id
     * @return void
     */
    public static function deleteAccessUser(int $id_file, int $id)
    {
        if (isset($_SESSION['ROLE'])) {
            if (isset($id_file) && isset($id)) {
                $sql = [];
                $sql['sql'] = "SELECT Users.id FROM `Users` WHERE Users.email=:email AND Users.first_name =:first_name";
                $sql['param'] = [
                    'email' => htmlspecialchars($_SESSION['EMAIL']),
                    'first_name' => htmlspecialchars($_SESSION['NAME']),
                ];
                $userData = SelectRow($sql['sql'], $sql['param']);

                if (!empty($userData)) {
                    $sql['sql'] = "SELECT Files.list_user_access FROM `Files` WHERE Files.id = :id_file";
                    $sql['param'] = [
                        'id_file' => htmlspecialchars($id_file),
                    ];
                    $fileData = SelectRow($sql['sql'], $sql['param']);

                    $arrAccessUser = json_decode($fileData[0]['list_user_access']);
                    foreach ($arrAccessUser as $key => $value) {
                        if ($value == $id) {
                            unset($arrAccessUser[$key]);
                        }
                    }

                    $sql['sql'] = "UPDATE `Files` SET Files.list_user_access = :list_user_access, Files.date_create = :date_create WHERE Files.id =:id_file";
                    $sql['param'] = [
                        'list_user_access' => json_encode($arrAccessUser),
                        'date_create' => date("Y-m-d H:i:s"),
                        'id_file' => htmlspecialchars($id_file),
                    ];
                    Query($sql['sql'], $sql['param']);
                    echo $message = "the user's access has been revoked";
                }
            } else {
                echo $message = " id file or id user not found";
            }
        } else {
            echo $message = "you need login-in";
        }
    }
}
