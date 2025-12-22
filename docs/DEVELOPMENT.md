# Разработка и эксплуатация

## Настройка веб-сервера

Для **Apache** необходимо разрешить `.htaccess` (`AllowOverride All`).
Для **Nginx** используйте проксирование в `fastcgi`:

```nginx
location / { try_files $uri $uri/ /index.html; }
```

## Добавление нового функционала

1. **Backend**: Создать обработчик в `api/handlers/`.
2. **Router**: Зарегистрировать `entity` в `api/index.php`.
3. **API Client**: Добавить метод в `js/api-client.js`.
4. **UI**: Описать рендер в `js/ui.js`.

## Устранение неполадок

- **Ошибка прав**: Убедитесь, что веб-сервер может писать в `db/comfort-rest.db`.
- **Сессии**: Если сессия не держится, проверьте `session.save_path` в конфиге PHP.
- **Логи**:
- `logs/error.log` — ошибки PHP.
- `logs/requests.log` — входящие API запросы.
