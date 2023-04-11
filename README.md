Here is the implementation of the REST API of cloud storage for files.

The author of the project: Victoria Babikova.

Use filestorage.sql to create a database.

You can interact with the application using the Postman utility.

List of API endpoints:

**
Перед вами реализация REST API облачного хранилища для файлов.

Автор проекта: Бабикова Виктория.

Используйте filestorage.sql, чтобы создать базу данных.

Вы можете взаимодействовать  с приложением с помощью утилиты Postman.

Список эндпоитов API:
**

GET /user/ 
 - Get a list of users (array)

GET /users?id=id 
 - Get a JSON object with information about a specific user

POST /user/  /** params: email, password, ?first_name */
 - Add user

PUT /user/ /** params: email, password, ?first_name */
 - Update user

GET /login?email=email&password=password
 - login-in 

GET /logout
 - logout

GET /reset_password?email=email&first_name=first_name
 - reset password

GET /change_password?email=email&first_name=first_name
 - change password

DELETE /user/?id=id 
 - Delete user

GET /admin/user/ 
 - List of users

GET /admin/user?id=id
 - Information on a specific user

DELETE /admin/user?id=id
 -Delete user

PUT /admin/user/ /** params: email, password, ?first_name */
 - Update user information 

GET /file/ 
 - List files

GET /file?id_file=id_file
 - Get information about a specific file

POST /file/ /** params: name_dir, file(uploaded file) */
 - Add File 

PUT /file/ /** params: (name_file, new_name_file) or (name_file, new_dir) */
 - Rename or move file 

DELETE /file?id_file=id_file 
 - Delete file

POST /directory/ /** params: name_dir */
 - Add Folder (directory) 

PUT /directory/ /** params: old_name_dir, new_name_dir */
 - Rename folder 

GET /directory?id_dir=id_dir
 - Get folder information (folder file list)

DELETE /directory?id_dir=id_dir
 - Delete folder

GET /user/search?email=email
 - User search by email

GET /files/share?id_file=id_file 
 - Get a list of users who have access to the file

PUT /files/share/ /** params: id_file, id(user_id) */
 - Add access to the file to the user with the id user_id


DELETE /files/share?id_file=id_file&id=id  
 - Terminate access to the file to the user with the user_id id
