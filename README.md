# NUSOAP_servicioWeb
Servicio web usando la librería NuSOAP de PHP para trabajar con una colección de grupos de música clasificados por su género.


El servidor tiene la siguiente base de datos donde se guarda la información de los grupos y los géneros:
	GÉNERO
		id_genero int  primary key auto_increment
		nombre varchar(100)
	GRUPO
		id_grupo int primary key auto_increment
		nombre varchar(100),
		género int, foreign key(genero) references genero(id_genero)
El servidor NuSOAP tiene las siguientes funciones:
- Una para insertar nuevos grupos de música en la base de datos. Esta función recibirá todos los campos de la tabla Grupo, excepto el identificador, que se autogenerará. 
- Una que devuelve un array con todos los géneros (identificador y nombre) que hay en la base de datos. 
- Una que devuelve un array con todos los grupos de un determinado género. La función recibe el género del cual se quiere obtener los grupos de música.

En el lado del cliente,hay una aplicación que permite dar de alta nuevos grupos, mediante un formulario. Los géneros se presentan al usuario mediante un desplegable que contiene todos los géneros que hay en la base de datos.

También puede verse en una tabla los grupos musicales de un determinado género. Éste lo seleccionará el usuario de un formulario (un desplegable).



