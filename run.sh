#!/bin/bash

# Color codes
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}=============================================${NC}"
echo -e "${BLUE}   M2M-SIM Docker Deployment (Raspberry Pi) ${NC}"
echo -e "${BLUE}=============================================${NC}"

# Check if Docker is installed
if ! [ -x "$(command -v docker)" ]; then
  echo -e "${RED}Error: Docker is not installed. Install it with:${NC}"
  echo -e "  curl -fsSL https://get.docker.com | sh"
  exit 1
fi

# Check if .env file exists, create from example if not
if [ ! -f .env ]; then
  echo -e "${YELLOW}[!] .env not found. Copying from .env.example...${NC}"
  cp .env.example .env
  echo -e "${RED}[!] PENTING: Edit file .env dan isi APP_KEY yang benar sebelum lanjut!${NC}"
  echo -e "${YELLOW}    Lalu jalankan ulang script ini.${NC}"
  exit 1
fi

# Check if APP_KEY is placeholder
if grep -q "GANTI_DENGAN_APP_KEY_ANDA" .env; then
  echo -e "${RED}[!] APP_KEY belum diisi di file .env!${NC}"
  echo -e "${YELLOW}    Isi APP_KEY Anda (dari project asal) di file .env, lalu jalankan ulang.${NC}"
  exit 1
fi

echo -e "${GREEN}[1/3] Pulling latest Docker images...${NC}"
docker compose pull

echo -e "${GREEN}[2/3] Starting all containers...${NC}"
docker compose up -d

echo -e "${GREEN}[3/3] Waiting for database to be ready...${NC}"
until docker compose exec db mysqladmin ping -h"localhost" --silent 2>/dev/null; do
  echo -e "${YELLOW}  Waiting for MariaDB...${NC}"
  sleep 2
done

echo -e "${BLUE}Running database migrations...${NC}"
docker compose exec app php artisan migrate --force

echo -e "${BLUE}Creating storage symbolic link...${NC}"
docker compose exec app php artisan storage:link 2>/dev/null || true

echo -e "${GREEN}=============================================${NC}"
echo -e "${GREEN}   SUKSES! M2M-SIM berjalan di Docker!       ${NC}"
echo -e "${GREEN}=============================================${NC}"
echo -e "Web App   : ${YELLOW}http://$(hostname -I | awk '{print $1}')${NC}"
echo -e "Admin     : ${YELLOW}http://$(hostname -I | awk '{print $1}')/admin${NC}"
echo -e "Hentikan  : ${YELLOW}docker compose down${NC}"
echo -e "Update    : ${YELLOW}docker compose pull && docker compose up -d${NC}"
echo -e "${GREEN}=============================================${NC}"
