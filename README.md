PRUEBA DE DIAGNOSTICO DESIS
-----------------------------------------------

Especificaciones Técnicas:
- Versión mínima de PHP utilizada: 7.3
- Versión de la base de datos utilizada: MySQL 10.3
---
Instrucciones de Instalación:
1. Descargar el archivo del proyecto.
2. Descomprimir el archivo en la ubicación deseada.
3. Crear una base de datos vacía en MySQL.
4. Importar el archivo SQL adjunto a la base de datos recién creada.
5. Actualizar datos de conexion en /database/config.php (Solo si aplica).
6. Utilizar servicio web apuntando a pagina ./index.html ubicada en la raiz del proyecto.

NOTA: Se deja habilitada una live demo: https://desis.dbr.cl
---
Estructura del Proyecto:

/database: contiene archivo con datos de conexion, ademas de un controlador que maneja las peticiones a la base de datos.

/logic: contiene logica que procesa la informacion enviada por el usuario, realizando validaciones backend y posteriormente llamando al controlador de base de datos para su insercion.

/public: contiene archivos html, javascript y estilos.
---
Notas:
- Proyecto desarrollado utilizando únicamente: HTML, PHP, JavaScript, AJAX y MySQL.
- Se asume que el sistema es para votaciones presidenciales, por lo que candidatos no se relaciona a regiones o comunas.
- Las comunas fueron consideradas como provincias para efectos de simplicidad.
