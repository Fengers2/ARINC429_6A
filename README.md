# CRUD de `instrumentos2`

CRUD en PHP nativo con PDO para `ARINC_429_6A`. Los formularios leen automáticamente las columnas de `instrumentos2` desde `INFORMATION_SCHEMA`, por lo que no se duplica ni se supone el esquema de MySQL.

## Configuración

```bash
export ARINC_DB_HOST=127.0.0.1
export ARINC_DB_NAME=ARINC_429_6A
export ARINC_DB_USER=tu_usuario_mysql
export ARINC_DB_PASS=tu_contraseña_mysql
php -S localhost:8000 -t public
```

La tabla debe tener una clave primaria. Abre `http://localhost:8000`.
