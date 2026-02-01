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

Сначала создайте и заполните .env (опционально)
```bash
cp .env.example .env
```
Запуск контейнеров
```bash
docker compose up -d --build
```
Настройка прав (если будет 502)
```bash
sudo chmod 777 -R ./
```
Генерация ключа приложения (опционально)
```bash
docker compose exec app php artisan key:generate
```
После запуска доступны:
- Web: http://localhost/
- API: http://localhost/api/notes

## Сидер
Чтобы засидить данные для тестов используйте
```bash
docker compose exec app php artisan db:seed
```

## Документация
Для создания документации API пропишите
```bash
docker compose exec app php artisan scribe:generate
```
После этого откроется доступ к http://localhost/docs
## Тесты
Чтобы проверить фукнционал API используйте данную команду.

```bash
docker compose exec app php artisan test --filter NoteApiTest
```
## Миграции
Запускаются автоматически при билде.
