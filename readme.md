# apocalipse_zumbi

API REST do apocalipse zumbi em Laravel 12 / PHP 8.4.

- Endpoints: `/api/sobreviventes`, `/api/inventario`, `/api/inventario/troca`, `/api/informar-zumbificacao`, `/api/relatorio-geral`
- Banco: script em `other/database.sql`
- Swagger: L5-Swagger (rota `/api/documentation` após `php artisan l5-swagger:generate`)
- Docker: `docker-compose up --build` (app + MySQL)
