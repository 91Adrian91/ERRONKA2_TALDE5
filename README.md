# Arrue Hortz Sintetikoak — web del laboratorio

Web PHP/MySQL del profesorado, corregida y adaptada al proyecto Arrue.

## Arrancar con Docker Desktop

Desde esta carpeta, la primera vez:

```powershell
Copy-Item .env.example .env
```

Elegir contraseñas propias en `.env` antes de inicializar la base. No volver
a copiar la plantilla encima de un `.env` existente. Después:

```powershell
docker compose up -d --build --wait
```

Abrir **http://127.0.0.1:8080**. PHP/Apache y MySQL están contenerizados.
La base no publica puerto al host y la web conecta con un usuario propio,
no con root. El arranque espera la salud de la base y de la aplicación.

Este entorno usa volúmenes propios del proyecto Compose; no reutiliza ni
modifica el contenedor antiguo `arrue-mysql`. El SQL inicial se importa
solo con el volumen vacío. Imágenes de productos y perfiles persisten en
sus respectivos volúmenes. `docker compose down` detiene el conjunto sin
borrar datos; no usar `down -v` si se quieren conservar.

Los usuarios del volcado son cuentas de demostración del laboratorio
(administrador `admin@bdweb`, contraseña `password`); no usar este conjunto
como servicio público con esas cuentas. El modo local es HTTP. Para HTTPS,
configurar `SESSION_COOKIE_SECURE=1` en el despliegue que termine TLS.

## Pruebas de seguridad aisladas

```powershell
docker compose --env-file .env.test.example -p arrue-security-test --profile test run --build --rm tests
```

El comando crea web, MySQL y un servidor con caducidad corta para las
pruebas. Usa `arrue_test`, puerto 8081 y volúmenes separados. No ejecutar
estos tests sobre datos reales. El resultado está en `build/security-http.json`;
las cuentas, productos e imágenes temporales de cada ejecución se limpian.

```powershell
docker compose --env-file .env.test.example -p arrue-security-test --profile test down
```

Con PHP 8.3+ y Composer instalados, las pruebas unitarias y PHPStan:

```powershell
composer install
composer check
```

## Instalaciones anteriores

Antes de usar una BD antigua, hacer su copia y ejecutar la migración del
índice único de usuarios con una cuenta que tenga permiso de alterar la tabla:

```powershell
php -d extension=mysqli tools/migrate-security.php
```

La herramienta se detiene si encuentra nombres duplicados; no elimina cuentas.
Para hashes heredados del ejercicio existe `tools/migrate-demo-passwords.php`:
sin argumentos informa; `--apply` migra solo los hashes de demostración
reconocidos. El volcado actual ya contiene hashes modernos.

Para mantener PHP local, el router reproduce los bloqueos de rutas internas:

```powershell
php -d extension=mysqli -S 127.0.0.1:8000 -t erronka2_web erronka2_web/router.php
```

## Evidencias y revisión

- [Inventario y pruebas de seguridad](docs/WEB-SEGURTASUNA.md).
- [Pipeline de GitHub Actions](docs/PIPELINEA.md).
- [Historial de auditoría](docs/auditoria/README.md).
- [Procedimiento de trabajo en equipo](CONTRIBUTING.md).

La CI ejecuta PHPStan y PHPUnit en PHP 8.3/8.5 y la suite HTTP en Compose.
Las pruebas documentan los flujos cubiertos; no constituyen una certificación
ni sustituyen una revisión independiente.
