#!/bin/bash

# Color codes for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}===============================================${NC}"
echo -e "${BLUE}    M2M-SIM Docker Orchestration Starter       ${NC}"
echo -e "${BLUE}===============================================${NC}"

# Check if Docker is installed
if ! [ -x "$(command -v docker)" ]; then
  echo -e "${RED}Error: Docker is not installed on this system.${NC}" >&2
  exit 1
fi

# Check if Docker Compose is installed
if ! [ -x "$(command -v docker-compose)" ] && ! docker compose version &> /dev/null; then
  echo -e "${RED}Error: Docker Compose is not installed on this system.${NC}" >&2
  exit 1
fi

# Check if .env file exists
if [ ! -f .env ]; then
  echo -e "${YELLOW}[!] .env file not found. Copying .env.example...${NC}"
  cp .env.example .env
  
  # Configure .env to use the docker db service by default
  sed -i 's/DB_HOST=127.0.0.1/DB_HOST=db/g' .env
  sed -i 's/DB_DATABASE=laravel/DB_DATABASE=laravel/g' .env
  sed -i 's/DB_USERNAME=root/DB_USERNAME=laravel_user/g' .env
  sed -i 's/DB_PASSWORD=aLP1aylX4IgTdwoXEw1MX669ot7VQgTa/DB_PASSWORD=laravel_password/g' .env
fi

echo -e "${GREEN}[1/3] Building and starting Docker containers...${NC}"
docker compose up -d --build

echo -e "${GREEN}[2/3] Waiting for the database to be ready...${NC}"
# Wait for MySQL/MariaDB to respond
until docker compose exec db mysqladmin ping -h"localhost" -u"laravel_user" -p"laravel_password" --silent; do
    echo -e "${YELLOW}Waiting for MariaDB connection...${NC}"
    sleep 2
done

echo -e "${GREEN}[3/3] Setting up Laravel application inside container...${NC}"

# Generate key if APP_KEY is empty in .env
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=base64" .env; then
  echo -e "${BLUE}Generating application key...${NC}"
  docker compose exec app php artisan key:generate
fi

# Set correct storage permissions
echo -e "${BLUE}Setting directory permissions...${NC}"
docker compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker compose exec app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run migrations and seeders
echo -e "${BLUE}Running migrations and seeding the database...${NC}"
docker compose exec app php artisan migrate --seed --force

# Create storage link
echo -e "${BLUE}Creating storage symbolic link...${NC}"
docker compose exec app php artisan storage:link

echo -e "${GREEN}===============================================${NC}"
echo -e "${GREEN}    SUCCESS: M2M-SIM is up and running!        ${NC}"
echo -e "${GREEN}===============================================${NC}"
echo -e "Web App:      ${YELLOW}http://localhost:8000${NC}"
echo -e "Filament:     ${YELLOW}http://localhost:8000/admin${NC}"
echo -e "Database Port:${YELLOW}33061${NC}"
echo -e "Stop Services: ${YELLOW}docker compose down${NC}"
echo -e "${GREEN}===============================================${NC}"
