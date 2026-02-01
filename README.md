# Notes (Laravel 12 + Vue + MySQL + Docker)

Тестовое приложение “Notes”: CRUD заметок через REST API + простой Vue-интерфейс, всё запускается в Docker.

---

## Стек

- Laravel 12 (API)
- Vue 3 (frontend, сборка через Vite)
- MySQL 8
- Nginx + PHP-FPM
- Docker Compose

---

## Быстрый старт (одной командой)

> Требования: установлен `Docker` и `Docker Compose`.

```bash
docker compose up -d --build
```

## Тесты
Чтобы проверить фукнционал API используйте данную команду.

```bash
docker compose exec app php artisan test --filter NoteApiTest
