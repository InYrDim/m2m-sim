# M2M SIP — Man 2 Makassar (Sistem Informasi Penjadwalan)

Sistem informasi manajemen untuk MAN 2 Kota Makassar, dibangun dengan Laravel.

---

## Dockerisasi

### Menarik Image

```bash
docker pull iyedeh/m2m-sim-app:latest
```

### Membangun Image

```bash
docker build -t iyedeh/m2m-sim-app:latest .
```

### Menjalankan dengan Docker Compose

```bash
# Salin .env dan atur APP_KEY
cp .env.example .env
# atau generate key baru: php artisan key:generate
docker compose up -d
```

### Memperbarui Setelah Ada Perubahan Kode

Di mesin build:

```bash
docker build -t iyedeh/m2m-sim-app:latest .
docker push iyedeh/m2m-sim-app:latest
```

Di server produksi:

```bash
docker pull iyedeh/m2m-sim-app:latest
docker compose down
docker compose up -d
```

Atau dalam satu perintah:

```bash
docker compose up -d --pull always
```

### Variabel Lingkungan

| Variable       | Default            | Keterangan                      |
|----------------|--------------------|---------------------------------|
| `APP_KEY`      | —                  | Kunci aplikasi Laravel (wajib)  |
| `APP_ENV`      | `local`            | `local`, `staging`, `production`|
| `APP_DEBUG`    | `true`             | Set `false` di production       |
| `DB_HOST`      | `db`               | Host MySQL (nama service Docker)|
| `DB_DATABASE`  | `laravel`          | Nama database                   |
| `DB_USERNAME`  | `root`             | User database                   |
| `DB_PASSWORD`  | —                  | Password database               |

### Checklist Produksi

- Set `APP_ENV=production` dan `APP_DEBUG=false`
- Generate `APP_KEY` unik via `php artisan key:generate`
- Gunakan `DB_PASSWORD` yang kuat
- Pastikan `APP_URL` dan `ASSET_URL` mengarah ke domain produksi
- Tambahkan network `cloudflare-bridge` (`docker network create cloudflare-bridge`) jika di belakang Cloudflare
- Gunakan volume atau service eksternal untuk penyimpanan persisten (log, uploads)

### Perintah Berguna

```bash
# Menjalankan Artisan command di dalam container
docker compose exec app php artisan migrate
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache

# Melihat log
docker compose logs -f app

# Restart service
docker compose restart
```
