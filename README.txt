Estructura sugerida del proyecto:

/govil
  /img
  /css
  /js
  /includes
    - db.php           (conexión a la base de datos)
    - header.php       (cabecera y menú de navegación)
    - footer.php       (pie de página)
  /pages
    - login.php        (login falso)
    - dashboard.php    (panel principal)
    - telas.php        (registro y listado de telas)
    - productos.php    (registro y listado de productos)
    - clientes.php     (registro y listado de clientes)
    - ordenes.php      (registro y listado de órdenes)
    - movimientos.php  (movimientos de inventario)
  index.php            (redirección o landing)
  estructura_bd.sql    (script de la base de datos)

Bootstrap se puede incluir vía CDN en header.php. 