<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Middleware\VerifyToken;
use App\Http\Middleware\ValidateRequest;

/**
* Inicio de Sesion para la obtención del Token de seguridad.
*
* @var
* email : Correo electronico del Usuario.
* password : Contraseña del Usuario.
* 
* @return
* (+)Token de autenticacion mas su tiempo de vida.
* (-)Mensaje de Error.
*
* @test
* "email": "prueba@prueba.com"
* "password": "1234"
*
*/
Route::post('/auth', [MainController::class, 'auth'])->middleware([ValidateRequest::class]);

/**
* Consulta la informacion de un Customer.
*
* @var
* type : Tipo de Consulta (dni, email).
* field : Campo para la busqueda del customer.
* 
* @return
* (+)Informacion del Customer mas los nombre de su region y comuna.
* (-)Mensaje de Error.
*
*/
Route::get('/getCustomers', [MainController::class, 'getInfo'])->middleware([ValidateRequest::class, VerifyToken::class]);

/**
* Crea un nuevo registro de Customer.
*
* @var
* dni : Dni del customer a registrar.
* email: Correo electronico.
* name : Nombre del customer
* last_name : Apellido del customer
* region : Id de la Region.
* commune : Id de la Comuna.
* 
* @return
* (+)Mensaje Existoso al crear.
* (-)Mensaje de Error.
*
*/
Route::post('/addCustomers', [MainController::class, 'addCustomer'])->middleware([ValidateRequest::class, VerifyToken::class]);

/**
* Elimina un Customer.
*
* @var
* dni : Dni del customer a eliminar.
* 
* @return
* (+)Mensaje Existoso al eliminar.
* (-)Mensaje de Error.
*
*/
Route::delete('/deleteCustomers', [MainController::class, 'deleteCustomer'])->middleware([ValidateRequest::class, VerifyToken::class]);
