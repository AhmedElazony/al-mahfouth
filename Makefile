NVM_USE = . ~/.nvm/nvm.sh && nvm use --lts
SHELL := /bin/bash

# SSL settings
SSL_DIR = ./docker/nginx/ssl
SSL_KEY = $(SSL_DIR)/private.key
SSL_CERT = $(SSL_DIR)/public.crt

# Color output
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[0;33m
RED := \033[0;31m
NC := \033[0m # No Color

# User/Group env vars
UID := $(shell id -u)
GID := $(shell id -g)

# Docker Compose files
LOCAL_COMPOSE := docker-compose.yml
DEV_COMPOSE := docker-compose.dev.yml
PROD_COMPOSE := docker-compose.prod.yml

# Targets
.PHONY: images install certs deploy undeploy

###### Local #######
images:
	@docker compose -f $(LOCAL_COMPOSE) build

install:
	docker compose -f $(LOCAL_COMPOSE) run --rm -u "$(UID):$(GID)" app composer install && \
	cp .env.example .env && \
	docker compose -f $(LOCAL_COMPOSE) run --rm -u "$(UID):$(GID)" app php artisan key:generate

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
	docker compose -f $(LOCAL_COMPOSE) run --rm -u "${UID}:${GID}" app bash

fix-permissions:
	@docker compose -f $(LOCAL_COMPOSE) run --rm -u "$(UID):$(GID)" app /var/www/html/docker/php/fix-permissions.sh

# Frontend commands
frontend-install:
	@echo "📦 Installing frontend dependencies..."
	@docker compose -f $(LOCAL_COMPOSE) run --rm -u "${UID}:${GID}" frontend npm install
	@echo "✅ Frontend dependencies installed!"

frontend-dev:
	@echo "🚀 Starting frontend dev server..."
	@docker compose -f $(LOCAL_COMPOSE) up frontend -d
	@echo "✅ Frontend running at http://localhost:5173"

frontend-build:
	@echo "🏗️ Building frontend for production..."
	@docker compose -f $(LOCAL_COMPOSE) run --rm -u "${UID}:${GID}" frontend npm run build
	@echo "✅ Frontend built!"

frontend-bash:
	@docker compose -f $(LOCAL_COMPOSE) run --rm -u "${UID}:${GID}" frontend sh

# Full stack commands
dev:
	@echo "🚀 Starting all services..."
	@docker compose -f $(LOCAL_COMPOSE) up -d
	@echo "✅ Backend: http://localhost:8080"
	@echo "✅ Frontend: http://localhost:5173"

# Docker management
up:
	@echo "🚀 Starting all services..."
	@docker compose -f $(LOCAL_COMPOSE) up -d
	@echo "✅ All services started!"

down:
	@echo "🛑 Stopping all services..."
	@docker compose -f $(LOCAL_COMPOSE) down
	@echo "✅ All services stopped!"

logs:
	@docker compose -f $(LOCAL_COMPOSE) logs -f

artisan:
	@docker compose run --rm -u "$(UID):$(GID)" app php artisan $(filter-out $@,$(MAKECMDGOALS))

composer:
	@docker compose run --rm -u "$(UID):$(GID)" app composer $(filter-out $@,$(MAKECMDGOALS))
#### END Local #####

#### Server management ######
### DEVELOPMENT ###
dev-up:
	@echo "$(BLUE)Starting development environment...$(NC)"
	@docker compose -f $(DEV_COMPOSE) up -d
	@echo "$(GREEN)Development environment started!$(NC)"

dev-down:
	@echo "$(YELLOW)Stopping development environment...$(NC)"
	@docker compose -f $(DEV_COMPOSE) down
	@echo "$(GREEN)Development environment stopped!$(NC)"

dev-build:
	@echo "$(BLUE)Building development environment...$(NC)"
	@docker compose -f $(DEV_COMPOSE) build --no-cache
	@echo "$(GREEN)Development environment built!$(NC)"

dev-restart:
	@echo "$(YELLOW)Restarting development environment...$(NC)"
	@docker compose -f $(DEV_COMPOSE) restart
	@echo "$(GREEN)Development environment restarted!$(NC)"

dev-logs: 
	@docker compose -f $(DEV_COMPOSE) logs -f

dev-ps:
	@docker compose -f $(DEV_COMPOSE) ps

dev-clean: ## Remove development containers and volumes (WARNING: deletes data)
	@echo "$(RED)WARNING: This will delete all data!$(NC)"
	@read -p "Are you sure? [y/N] " -n 1 -r; \
	echo; \
	if [[ $$REPLY =~ ^[Yy]$$ ]]; then \
		docker compose -f $(DEV_COMPOSE) down -v; \
		echo "$(GREEN)Development environment cleaned!$(NC)"; \
	fi

dev-fix-permissions:
	@docker compose -f $(DEV_COMPOSE) run --rm -u "$(UID):$(GID)" app sh -c "chmod +x /var/www/html/docker/php/fix-permissions.sh && /var/www/html/docker/php/fix-permissions.sh"

dev-composer-install:
	@docker compose -f $(DEV_COMPOSE) run --rm -u "$(UID):$(GID)" app composer install \
		&& echo "$(GREEN)Composer dependencies installed!$(NC)" \
		&& docker compose -f $(DEV_COMPOSE) run --rm -u "$(UID):$(GID)" app php artisan key:generate \
		&& echo "$(GREEN)Application key generated!$(NC)"

dev-npm-install:
	@docker compose -f $(DEV_COMPOSE) run --rm -u "$(UID):$(GID)" frontend npm install

dev-npm-build:
	@docker compose -f $(DEV_COMPOSE) run --rm -u "$(UID):$(GID)" frontend npm run build

dev-copy-frontend:
	@echo "$(GREEN)Copying frontend build to public/app...$(NC)"
	@rm -rf public/app
	@cp -r frontend/dist public/app
	@echo "$(GREEN)Frontend build copied!$(NC)"

dev-setup:
	@echo "$(BLUE)Setting up development environment...$(NC)"
	@if [ ! -f .env ]; then cp .env.example .env; echo "$(GREEN).env file created$(NC)"; fi
	@if [ ! -f frontend/.env.development ]; then cp frontend/.env.example frontend/.env.development; echo "$(GREEN)frontend/.env.development created$(NC)"; fi
	@$(MAKE) dev-build
	@$(MAKE) dev-up
	@echo "$(YELLOW)Waiting for containers to be ready...$(NC)"
	@sleep 10
	@echo "$(GREEN)Running composer install...$(NC)"
	@$(MAKE) dev-composer-install
	@echo "$(GREEN)Running migrations...$(NC)"
	@$(MAKE) dev-artisan migrate --seed
	@$(MAKE) dev-npm-install
	@$(MAKE) dev-npm-build
	@$(MAKE) dev-copy-frontend
	@$(MAKE) dev-fix-permissions
	@echo "$(GREEN)Permissions fixed!$(NC)"
	@docker compose -f $(DEV_COMPOSE) app php artisan storage:link
	@docker compose -f $(DEV_COMPOSE) app php artisan optimize 
	@echo "$(GREEN)Development environment setup complete!$(NC)"
	@echo "$(BLUE)You can access the app at http://$(curl -s ifconfig.me)$(NC)"

dev-deploy:
	@echo "$(BLUE)Deploying development environment...$(NC)"
	@git pull origin develop
	@docker compose -f $(DEV_COMPOSE) app php artisan down 
	@$(MAKE) dev-build
	@$(MAKE) dev-up
	@echo "$(YELLOW)Waiting for containers to be ready...$(NC)"
	@sleep 10
	@echo "$(GREEN)Running composer install...$(NC)"
	@$(MAKE) dev-composer-install
	@echo "$(GREEN)Running migrations...$(NC)"
	@$(MAKE) dev-artisan migrate
	@$(MAKE) dev-npm-install
	@$(MAKE) dev-npm-build
	@$(MAKE) dev-copy-frontend	
	@docker compose -f $(DEV_COMPOSE) app php artisan optimize 
	@docker compose -f $(DEV_COMPOSE) app php artisan up
	@echo "$(GREEN)Development environment deployed!$(NC)"

### PRODUCTION ###
prod-up:
	@echo "$(BLUE)Starting production environment...$(NC)"
	@docker compose -f $(PROD_COMPOSE) up -d
	@echo "$(GREEN)Production environment started!$(NC)"

prod-down:
	@echo "$(YELLOW)Stopping production environment...$(NC)"
	@docker compose -f $(PROD_COMPOSE) down
	@echo "$(GREEN)Production environment stopped!$(NC)"

prod-build:
	@echo "$(BLUE)Building production containers...$(NC)"
	@docker compose -f $(PROD_COMPOSE) build --no-cache
	@echo "$(GREEN)Production containers built!$(NC)"

prod-restart:
	@echo "$(BLUE)Restarting production environment...$(NC)"
	@docker compose -f $(PROD_COMPOSE) restart
	@echo "$(GREEN)Production environment restarted!$(NC)"

prod-logs:
	@docker compose -f $(PROD_COMPOSE) logs -f

prod-ps:
	@docker compose -f $(PROD_COMPOSE) ps

prod-fix-permissions:
	@docker compose -f $(PROD_COMPOSE) run --rm -u "$(UID):$(GID)" app sh -c "chmod +x /var/www/html/docker/php/fix-permissions.sh && /var/www/html/docker/php/fix-permissions.sh"

prod-composer-install:
	@docker compose -f $(PROD_COMPOSE) run --rm -u "$(UID):$(GID)" app composer install --no-dev \
		&& echo "$(GREEN)Composer dependencies installed!$(NC)" \
		&& docker compose -f $(PROD_COMPOSE) run --rm -u "$(UID):$(GID)" app php artisan key:generate \
		&& echo "$(GREEN)Application key generated!$(NC)"

prod-build-frontend:
	@cd frontend && npm install --production && npm run build

prod-copy-frontend:
	@echo "$(GREEN)Copying frontend build to public/app...$(NC)"
	@rm -rf public/app
	@cp -r frontend/dist public/app
	@echo "$(GREEN)Frontend build copied!$(NC)"

prod-artisan:
	@docker compose -f $(PROD_COMPOSE) run --rm -u "$(UID):$(GID)" app php artisan $(filter-out $@,$(MAKECMDGOALS))

prod-composer:
	@docker compose -f $(PROD_COMPOSE) run --rm -u "$(UID):$(GID)" app composer $(filter-out $@,$(MAKECMDGOALS))

prod-setup:
	@echo "$(BLUE)Setting up production environment...$(NC)"
	@if [ ! -f .env ]; then echo "$(RED).env file is missing!$(NC)" exit 1; fi
	@if [ ! -f frontend/.env.production ]; then echo "$(RED)frontend/.env.production file is missing!$(NC)" exit 1; fi
	@$(MAKE) prod-build
	@$(MAKE) prod-up
	@echo "$(YELLOW)Waiting for containers to be ready...$(NC)"
	@sleep 10
	@echo "$(GREEN)Running composer install...$(NC)"
	@$(MAKE) prod-composer-install
	@echo "$(GREEN)Running migrations...$(NC)"
	@$(MAKE) prod-artisan migrate --seed
	@$(MAKE) prod-build-frontend
	@$(MAKE) prod-copy-frontend
	@$(MAKE) prod-fix-permissions
	@echo "$(GREEN)Permissions fixed!$(NC)"
	@docker compose -f $(PROD_COMPOSE) app php artisan storage:link
	@docker compose -f $(PROD_COMPOSE) app php artisan optimize
	@echo "$(GREEN)Production environment setup complete!$(NC)"

prod-deploy:
	@echo "$(BLUE)Deploying production environment...$(NC)"
	@git pull origin main
	@docker compose -f $(PROD_COMPOSE) app php artisan down 
	@$(MAKE) prod-build
	@$(MAKE) prod-up
	@echo "$(YELLOW)Waiting for containers to be ready...$(NC)"
	@sleep 10
	@echo "$(GREEN)Running composer install...$(NC)"
	@$(MAKE) prod-composer-install
	@echo "$(GREEN)Running migrations...$(NC)"
	@$(MAKE) prod-artisan migrate
	@$(MAKE) prod-build-frontend
	@echo "$(GREEN)Copying frontend build to public/app...$(NC)"
	@$(MAKE) prod-copy-frontend
	@docker compose -f $(PROD_COMPOSE) app php artisan optimize 
	@docker compose -f $(PROD_COMPOSE) app php artisan up
	@echo "$(GREEN)Production environment deployed!$(NC)"

prod-ssl-renew:
	@echo "$(BLUE)Renewing SSL certificates...$(NC)"
	@sudo certbot renew
	@docker compose -f $(PROD_COMPOSE) exec nginx nginx -s reload
	@echo "$(GREEN)SSL certificates renewed!$(NC)"
### END PRODUCTION ###
### END Server management ######

%:
	@: