NVM_USE = . ~/.nvm/nvm.sh && nvm use --lts
SHELL := /bin/bash

# SSL settings
SSL_DIR = ./docker/nginx/ssl
SSL_KEY = $(SSL_DIR)/private.key
SSL_CERT = $(SSL_DIR)/public.crt

# User/Group env vars
UID := $(shell id -u)
GID := $(shell id -g)

# Targets
.PHONY: images install certs deploy undeploy bash fix-permissions frontend-install frontend-dev frontend-build frontend-bash dev artisan tinker up down logs

images:
	@docker compose build

install:
	@docker compose run --rm -u "$(UID):$(GID)" app composer install && \
	cp .env.example .env && \
	docker compose run --rm -u "$(UID):$(GID)" app php artisan key:generate

certs:
	@if [ -f $(SSL_KEY) ] && [ -f $(SSL_CERT) ]; then \
		echo "SSL certificate already exists:"; \
		echo "   → $(SSL_KEY)"; \
		echo "   → $(SSL_CERT)"; \
	else \
		mkdir -p $(SSL_DIR); \
		openssl req -x509 -nodes -days 365 \
			-newkey rsa:2048 \
			-keyout $(SSL_KEY) \
			-out $(SSL_CERT) \
			-subj "/C=OM/ST=Muscat/L=Oman/O=AI/CN=localhost"; \
		echo "✅ SSL certificate generated."; \
	fi

bash:
	@docker compose run --rm -u "${UID}:${GID}" app bash

fix-permissions:
	@docker compose run --rm -u "$(UID):$(GID)" app /var/www/html/docker/php/fix-permissions.sh

# Frontend commands
frontend-install:
	@echo "📦 Installing frontend dependencies..."
	@docker compose run --rm -u "${UID}:${GID}" frontend npm install
	@echo "✅ Frontend dependencies installed!"

frontend-dev:
	@echo "🚀 Starting frontend dev server..."
	@docker compose up frontend -d
	@echo "✅ Frontend running at http://localhost:5173"

frontend-build:
	@echo "🏗️ Building frontend for production..."
	@docker compose run --rm -u "${UID}:${GID}" frontend npm run build
	@echo "✅ Frontend built!"

frontend-bash:
	@docker compose run --rm -u "${UID}:${GID}" frontend sh

# Full stack commands
dev:
	@echo "🚀 Starting all services..."
	@docker compose up -d
	@echo "✅ Backend: http://localhost:8080"
	@echo "✅ Frontend: http://localhost:5173"

# Artisan commands
artisan:
	@docker compose run --rm -u "${UID}:${GID}" app php artisan $(filter-out $@,$(MAKECMDGOALS))

tinker:
	@docker compose run --rm -u "${UID}:${GID}" -e HOME=/tmp app php artisan tinker

%:
	@:

# Docker management
up:
	@echo "🚀 Starting all services..."
	@docker compose up -d
	@echo "✅ All services started!"

down:
	@echo "🛑 Stopping all services..."
	@docker compose down
	@echo "✅ All services stopped!"

logs:
	@docker compose logs -f
