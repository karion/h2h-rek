# h2h-rek

Projekt oparty o **Symfony + FrankenPHP**, z bazą **PostgreSQL** i **Redis**, uruchamiany w Dockerze.
Środowisko developerskie jest w pełni konteneryzowane, działa bez uprawnień root i obsługiwane przez `Makefile`.

---

## 📦 Stack technologiczny

- PHP (FrankenPHP / Caddy)
- Symfony 7.4
- PostgreSQL 16
- Redis 7
- Docker + Docker Compose
- Makefile (DX / skróty poleceń)

---

## ⚙️ Wymagania

- Docker >= 24
- Docker Compose v2
- GNU Make
- Linux / WSL2 / macOS

---

## 🔐 Zmienne środowiskowe

Plik **`.env.docker`** (na podstawie **`.env.docker.dist`**):

```env
APP_ENV=dev

POSTGRES_DB=h2h_rek_db
POSTGRES_USER=postgres_user
POSTGRES_PASSWORD=postgres_pass

DATABASE_URL=postgresql://postgres_user:postgres_pass@db:5432/h2h_rek_db?serverVersion=16&charset=utf8

REDIS_URL=redis://redis:6379

# UID/GID użytkownika z hosta (opcjonalnie – Makefile ustawia automatycznie)
PUID=1000
PGID=1000
```

## ▶️ Uruchomienie projektu

### Build i start kontenerów
```bash
make build
```
### Wejście do kontenera aplikacji
```bash
make bash
```

### Instalacja zależności PHP
```bash
make install
```

### Migracje bazy danych
```bash
make migrate
```

Dokumentacja API dostępna pod:

http://localhost:8080/api


