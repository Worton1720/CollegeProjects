# Архитектура и API

## Схема базы данных

### Таблицы:

- **Countries**: `id, name, visa_required`.
- **Clients**: `id, full_name, phone, passport_data`.
- **Tours**: `id, title, country_id (FK), start_date, price, client_id (FK)`.
- **Admins**: `id, username, password (hash)`.

## API Reference

Базовый URL: `/api/index.php?entity={entity}&action={action}`

| Entity      | Actions                                 | Описание            |
| :---------- | :-------------------------------------- | :------------------ |
| `auth`      | `login`, `logout`, `check`              | Управление доступом |
| `tours`     | `list`, `create`, `update`, `delete`    | Управление турами   |
| `clients`   | `list`, `create`, `quick-add`, `delete` | Работа с клиентами  |
| `countries` | `list`, `create`, `delete`              | Справочник стран    |

### Формат ответа

```json
{
  "status": "success",
  "data": { ... },
  "timestamp": "2025-12-22..."
}
```
