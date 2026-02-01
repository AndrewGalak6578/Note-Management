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

## Быстрый старт 

> Требования: установлен `Docker` и `Docker Compose`.

Сначала создайте и заполните .env
```bash
cp .env.example .env
```
Запуск контейнеров
```bash
docker compose up -d --build
```
Настройка прав
```bash
sudo chmod 777 -R ./
```
Генерация ключа приложения
```bash
docker compose exec app php artisan key:generate
```
После запуска доступны:
- Web: http://localhost/
- API: http://localhost/api/notes
## Тесты
Чтобы проверить фукнционал API используйте данную команду.

```bash
docker compose exec app php artisan test --filter NoteApiTest
```

