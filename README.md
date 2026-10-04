# Pedidos Shein

Clientes cotizan y envían pedidos desde el celular (`/`); la administradora gestiona lotes, agotados, pagos y ganancia en `/admin`.

## Local (sin PHP instalado)
```sh
docker run --rm -it -u $(id -u):$(id -g) -v $PWD:/app -w /app -p 8000:8000 \
  -e ADMIN_EMAIL=admin@test.com -e ADMIN_PASSWORD=secreto123 composer:latest \
  sh -c "php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0"
```
Pruebas: `docker run --rm -v $PWD:/app -w /app composer:latest php artisan test`

## Railway
1. Nuevo proyecto desde el repo + servicio Postgres.
2. Variables del servicio web:
   `APP_KEY` (salida de `php artisan key:generate --show`), `APP_ENV=production`, `APP_DEBUG=false`,
   `APP_URL=https://<dominio>`, `DB_CONNECTION=pgsql`, `DB_URL=${{Postgres.DATABASE_URL}}`,
   `ADMIN_EMAIL`, `ADMIN_PASSWORD`.
3. `railway.json` corre migraciones y crea/actualiza la cuenta admin en cada deploy.

## Servidor los-primos (opcional)
Igual que cualquier Laravel: `DB_CONNECTION=mysql` + credenciales, `php artisan migrate --force && php artisan db:seed --force`.
