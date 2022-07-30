# api-test
Restful Api for knowledge average.

# DOCUMENTACIÓN

## Requisitos
Mysql (Version usada durante el desarrollo v5.7.33).
Composer (Version usada durante el desarrollo v2.2.4).
Todos los estipulados en la documentación del Framework.
Enlace: https://laravel.com/docs/9.x/deployment#server-requirements

## Instalación
1. Clonar el Repositorio de la siguiente dirección: https://github.com/wolfrex2809/api-test.git
2. En caso de implementarse a con "Forge" o "Vapor", seguir la Documentación Oficial
   Enlace: https://laravel.com/docs/9.x/deployment#deploying-with-forge-or-vapor
3. En caso de implementar con "Nginx", seguir la Documentación Oficial
   Enlace: https://laravel.com/docs/9.x/deployment#nginx
4. En caso de implementar a traves de Apache, Copiar archivos de la raiz en la carpeta designada por el "HTTP Server"

## Configuración (HTTP Server Apache)
1. Crear archivo de configuracion usando la siguente estructura:
	
<VirtualHost *:80>
    ServerAdmin admin@example.com
    ServerName mydomain.com
    DocumentRoot /var/www/html/laravel/public

    <Directory /var/www/html/laravel>
	    Options Indexes MultiViews
	    AllowOverride None
	    Require all granted
	</Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>

2. Cambiar el "DocumentRoot" por la ruta donde se encuentra la carpeta "public " del "Producto"
3. Cambiar la Etiqueta "Directory" por la ruta donde se encuentra la Raiz del "Producto"
4. En la raiz del "Producto", ejecutar el comando "composer install --no-dev" 
(Esto instalara todas las dependencia de laravel usadas en Producción)
5. Otorgar los permisos en varios directorios del "Producto", 
tales como: "/{Raiz}", "/{Raiz}/storage", "/{Raiz}/bootstrap/cache";
6. Se crea el Archivo de Variables de Entorno ".env" tomando como ejemplo en archivo ".env.example".
7. Se configura el archivo ".env", cambiando la variable "APP_DEBUG" en false, en caso de estar true
8. En el mismo archivo se cambia la configuracion de la Base de Datos "Mysql", 
colocando su "Ruta" (En caso de estar instalada en el mismo servidor dejar igual),
   su "Usuario", "Contaseña" y "Puerto usado".


## Configuración Base de Datos (Mysql)

1. Usar los siguientes Queries para crear la Estructura:


DROP SCHEMA IF EXISTS `mydb` ;

CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 COLLATE
utf8_general_ci ;
USE `mydb` ;

DROP TABLE IF EXISTS `mydb`.`regions` ;

CREATE TABLE IF NOT EXISTS `mydb`.`regions` (
`id_reg` INT NOT NULL AUTO_INCREMENT COMMENT '', `description` VARCHAR(90)
NOT NULL COMMENT '',
`status` ENUM('A', 'I', 'trash') NOT NULL DEFAULT 'A' COMMENT '', PRIMARY KEY
(`id_reg`) COMMENT '')
ENGINE = MyISAM;

DROP TABLE IF EXISTS `mydb`.`communes` ;

CREATE TABLE IF NOT EXISTS `mydb`.`communes` (
`id_com` INT NOT NULL AUTO_INCREMENT COMMENT '', `id_reg` INT NOT NULL
COMMENT '',
`description` VARCHAR(90) NOT NULL COMMENT '',
`status` ENUM('A', 'I', 'trash') NOT NULL DEFAULT 'A' COMMENT '', PRIMARY KEY
(`id_com`, `id_reg`) COMMENT '',
INDEX `fk_communes_region_idx` (`id_reg` ASC) COMMENT '')
ENGINE = MyISAM;

DROP TABLE IF EXISTS `mydb`.`customers` ;

CREATE TABLE IF NOT EXISTS `mydb`.`customers` (
`dni` VARCHAR(45) NOT NULL COMMENT 'Documento de Identidad',
`id_reg` INT NOT NULL COMMENT '',
`id_com` INT NOT NULL COMMENT '',
`email` VARCHAR(120) NOT NULL COMMENT 'Correo Electrónico',
`name` VARCHAR(45) NOT NULL COMMENT 'Nombre',
`last_name` VARCHAR(45) NOT NULL COMMENT 'Apellido',
`address` VARCHAR(255) NULL COMMENT 'Dirección',
`date_reg` DATETIME NOT NULL COMMENT 'Fecha y hora del registro',
`status` ENUM('A', 'I', 'trash') NOT NULL DEFAULT 'A' COMMENT 'estado del registro:\nA
: Activo\nI : Desactivo\ntrash : Registro eliminado',
PRIMARY KEY (`dni`, `id_reg`, `id_com`) COMMENT '',
INDEX `fk_customers_communes1_idx` (`id_com` ASC, `id_reg` ASC) COMMENT '',
UNIQUE INDEX `email_UNIQUE` (`email` ASC) COMMENT '')
ENGINE = MyISAM;


DROP TABLE IF EXISTS `mydb`.`users` ;

CREATE TABLE `mydb`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `description` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `date_reg` DATETIME NULL,
  `status` ENUM('A', 'I', 'trash') NOT NULL,
  PRIMARY KEY (`id`))
COMMENT = 'Tabla para la autenticacion al realizar cualquier solicitud';

DROP TABLE IF EXISTS `mydb`.`user_token` ;

CREATE TABLE `mydb`.`user_token` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user` INT NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `ip` VARCHAR(45) NOT NULL,
  `date_reg` DATETIME NOT NULL,
  `lifetime` INT NOT NULL,
  `status` ENUM('A', 'I', 'trash') NOT NULL COMMENT 'Tabla para la autenticacion a traves de token.',
  PRIMARY KEY (`id`),
  INDEX `user_idx` (`user` ASC),
  CONSTRAINT `user`
    FOREIGN KEY (`user`)
    REFERENCES `mydb`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

DROP TABLE IF EXISTS `mydb`.`logs` ;

CREATE TABLE `mydb`.`logs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `type` ENUM('I', 'O') NOT NULL,
  `request` VARCHAR(255) NOT NULL,
  `data` TEXT NOT NULL,
  `output` VARCHAR(255) NULL,
  `comments` VARCHAR(255) NULL,
  `user` INT NULL,
  `ip` VARCHAR(45) NOT NULL,
  `date_reg` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `user_idx` (`user` ASC),
  CONSTRAINT `user_log`
    FOREIGN KEY (`user`)
    REFERENCES `mydb`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

INSERT INTO `mydb`.`regions` (`id_reg`,`description`, `status`) VALUES ('1' ,'prueba reg', 'A');
INSERT INTO `mydb`.`communes` (`id_reg`, `description`, `status`) VALUES ('1', 'prueba com', 'A');

INSERT INTO `mydb`.`users` (`id`, `description`, `email`, `password`, `date_reg`, `status`) 
VALUES (NULL, 'usuario prueba', 'prueba@prueba.com', 
'$2y$10$siB5cZA3EyeOKULWgdkUmef0BN54R8ncpqVcdZV.7NRl9n4HsUUpi', '2022-07-29 12:01:00', 'A');


## Uso

1. Para ejecutar las pruebas puede usar un Cliente HTTP como "Postman"
2. Ejecutar cualquiera de las Rutas especificadas aca:

	2.1 Ruta: '{Url Base}/auth', Metodo: "POST", Datos Requeridos (Colocar en el Body como un Raw de Json): 
	{ 
		"email": "prueba@prueba.com", 
		"password": "1234"
	} (Estos son datos de prueba, esta ruta retornara un token el cual sera usado en las posteriores como "Bearer Token",
	 de no poseerlo no permitira el acceso al resto de las Rutas).

	2.2  Ruta: '{Url Base}/getCustomers', Metodo: "GET", Datos Requeridos (Colocar en el Body como un Raw de Json): 
	{ 
		"type": "dni/email", 
		"field": ""
	} (
		"type" puede ser solo "dni" o "email", 
		"field" Este representa Campo con el que se buscara el customer, de ser email el correo y de ser dni el dni
	).
	2.3  Ruta: '{Url Base}/addCustomers', Metodo: "POST", Datos Requeridos (Colocar en el Body como un Raw de Json): 
	{
	    "dni": "",
	    "email": "",
	    "name": "",
	    "last_name": "",
	    "address": "",
	    "region": ,
	    "commune": ,
	}(
		"dni" el dni del customer a registrar, 
		"email" El correo electronico del customer(Debe cumplir con la estructura de un correo),
		"name" Nombre del customer,
		"last_name" Apellido del customer,
		"address" Direccion del customer (Es opcional)
		"region" Id de la region "1" por defecto,
		"commune" Id de la comuna "1" por defecto,
		(Solo se aceptaran una comuna y una region esten relacionadas entre si).
	).
	2.4 Ruta: '{Url Base}/deleteCustomers', Metodo: "DELETE", Datos Requeridos (Colocar en el Body como un Raw de Json): 
	{
    	"dni": "" 
	}
	("dni" el dni del customer a eliminar).

	Cualquier rutas usada fuera de estas cuatro retornara un Error 404 (Page not found).