# M2M-SIM Docker Deployment

Deployment branch untuk menjalankan M2M-SIM menggunakan Docker image dari Docker Hub.
**Tidak memerlukan source code PHP sama sekali.**

## Struktur File

```
.
├── docker-compose.yml        # Konfigurasi service (App, Nginx, MariaDB)
├── docker/nginx/default.conf # Konfigurasi Nginx
├── .env.example              # Template environment variables
├── .env                      # File env aktif (TIDAK di-commit, buat sendiri)
└── run.sh                    # Script otomatis untuk jalankan & setup
```

## Cara Menjalankan

### 1. Clone hanya branch ini
```bash
git clone --branch dockerize --single-branch https://github.com/iyedeh/m2m-sim.git
cd m2m-sim
```

### 2. Buat file .env dari template
```bash
cp .env.example .env
nano .env  # Edit APP_KEY dan sesuaikan konfigurasi lainnya
```

> **Penting:** Isi `APP_KEY` dengan value dari project asal Anda.

### 3. Jalankan dengan script otomatis
```bash
chmod +x run.sh
./run.sh
```

Script akan otomatis:
- Pull image terbaru dari Docker Hub (`iyedeh/m2m-sim-app:latest`)
- Menyalakan semua container (App, Nginx, MariaDB)
- Menunggu database siap
- Menjalankan migrasi database

### Perintah Berguna

```bash
# Lihat status container
docker compose ps

# Lihat log aplikasi
docker compose logs -f app

# Hentikan semua container
docker compose down

# Update ke image terbaru
docker compose pull && docker compose up -d

# Masuk ke shell container
docker compose exec app bash
```

## Requirements

- Docker Engine (Linux/Raspberry Pi: `curl -fsSL https://get.docker.com | sh`)
- Docker Compose Plugin
- Minimal 512MB RAM (Raspberry Pi 4/5 disarankan)
