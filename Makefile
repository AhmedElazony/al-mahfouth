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
.PHONY: images install certs prepare-server deploy-prod bash fix-permissions frontend-install frontend-dev frontend-build frontend-bash dev artisan tinker up down logs

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

# Deploy on a Server (Production)
prepare-server:
	@echo "🔧 Preparing server environment for production..."
	@echo "🔍 Checking .env file..."
	@if [ ! -f .env ]; then \
        echo "❌ .env file not found! Please create one from .env.example"; \
        exit 1; \
	fi
	@echo "🏗️ Building Docker images..."
	@docker compose -f docker-compose.prod.yml build --no-cache
	@echo "📦 Installing backend dependencies (production)..."
	@docker compose -f docker-compose.prod.yml run --rm -u "$(UID):$(GID)" app composer install --no-dev --optimize-autoloader --no-interaction
	@echo "🔑 Generating application key (if not exists)..."
	@docker compose -f docker-compose.prod.yml run --rm -u "$(UID):$(GID)" app php artisan key:generate --force
	@echo "📦 Building frontend for production..."
	@docker compose run --rm -u "${UID}:${GID}" -w /var/www/html/frontend frontend npm install --production=false
	@docker compose run --rm -u "${UID}:${GID}" -w /var/www/html/frontend frontend npm run build
	@echo "📁 Copying frontend build to public directory..."
	@mkdir -p ./public/app
	@cp -r ./frontend/dist/* ./public/app/
	@echo "🚀 Starting production services..."
	@docker compose -f docker-compose.prod.yml up -d
	@echo "⏳ Waiting for services to be ready..."
	@sleep 10
	@echo "🗄️ Running database migrations..."
	@docker compose -f docker-compose.prod.yml exec -u "$(UID):$(GID)" app php artisan migrate --force
	@echo "🔗 Creating storage link..."
	@docker compose -f docker-compose.prod.yml exec -u "$(UID):$(GID)" app php artisan storage:link
	@echo "⚡ Optimizing application..."
	@docker compose -f docker-compose.prod.yml exec -u "$(UID):$(GID)" app php artisan config:cache
	@docker compose -f docker-compose.prod.yml exec -u "$(UID):$(GID)" app php artisan route:cache
	@docker compose -f docker-compose.prod.yml exec -u "$(UID):$(GID)" app php artisan view:cache
	@echo "🔧 Setting correct permissions..."
	@make fix-permissions
	@echo "✅ Production environment ready!"
	@echo ""
	@echo "📝 Next steps:"
	@echo "   1. Configure your domain DNS to point to this server"
	@echo "   2. Run: make setup-ssl DOMAIN=yourdomain.com"
	@echo "   3. Update .env with production values"
	@echo "   4. Access your app at: https://yourdomain.com"

# Setup SSL with Let's Encrypt
setup-ssl:
	@echo "🔒 Setting up SSL certificate..."
	@if [ -z "$(DOMAIN)" ]; then \
        echo "❌ Please provide DOMAIN: make setup-ssl DOMAIN=yourdomain.com"; \
        exit 1; \
    fi
	@echo "🛑 Stopping nginx temporarily..."
	@docker compose -f docker-compose.prod.yml stop nginx
	@echo "📜 Obtaining SSL certificate from Let's Encrypt..."
	@sudo certbot certonly --standalone -d $(DOMAIN) -d www.$(DOMAIN) --non-interactive --agree-tos --email admin@$(DOMAIN)
	@echo "✅ SSL certificate obtained!"
	@echo "🚀 Starting nginx..."
	@docker compose -f docker-compose.prod.yml up -d nginx
	@echo "🔒 SSL setup completed for $(DOMAIN)"

# Deploy/Update production
deploy-prod:
	@echo "🚀 Deploying updates to production..."
	@echo "🛑 Putting application in maintenance mode..."
	@docker compose -f docker-compose.prod.yml exec app php artisan down
	@echo "📥 Pulling latest changes..."
	@git pull origin master
	@echo "🏗️ Rebuilding images..."
	@docker compose -f docker-compose.prod.yml build
	@echo "📦 Updating dependencies..."
	@docker compose -f docker-compose.prod.yml run --rm -u "$(UID):$(GID)" app composer install --no-dev --optimize-autoloader --no-interaction
	@echo "🗄️ Running migrations..."
	@docker compose -f docker-compose.prod.yml exec -u "$(UID):$(GID)" app php artisan migrate --force
	@echo "📦 Rebuilding frontend..."
	@docker compose run --rm -u "${UID}:${GID}" -w /var/www/html/frontend frontend npm install --production=false
	@docker compose run --rm -u "${UID}:${GID}" -w /var/www/html/frontend frontend npm run build
	@mkdir -p ./public/app
	@cp -r ./frontend/dist/* ./public/app/
	@echo "⚡ Optimizing..."
	@docker compose -f docker-compose.prod.yml exec -u "$(UID):$(GID)" app php artisan config:cache
	@docker compose -f docker-compose.prod.yml exec -u "$(UID):$(GID)" app php artisan route:cache
	@docker compose -f docker-compose.prod.yml exec -u "$(UID):$(GID)" app php artisan view:cache
	@echo "🔄 Restarting services..."
	@docker compose -f docker-compose.prod.yml restart app
	@echo "✅ Bringing application back online..."
	@docker compose -f docker-compose.prod.yml exec app php artisan up
	@echo "🎉 Deployment completed!"

setup-nginx-prod:
	@echo "🔧 Setting up production nginx config..."
	@read -p "Enter your domain (e.g., example.com): " DOMAIN; \
	sed "s/yourdomain.com/$$DOMAIN/g" docker/nginx/production.conf > docker/nginx/production.tmp.conf && \
	mv docker/nginx/production.tmp.conf docker/nginx/production.conf
	@echo "✅ Nginx production config updated!"
	@echo "📝 Remember to update your .env file with the same domain"

# Create certbot directory
prepare-certbot:
	@mkdir -p docker/nginx/certbot
	@echo "✅ Certbot directory created"

# Full server setup (development)
prepare-server-dev:
	@echo "🔧 Preparing server environment for development/testing..."
	@echo ""
	@echo "🔍 Checking .env file..."
	@if [ ! -f .env ]; then \
        echo "❌ .env file not found! Please create one from .env.example"; \
		echo " creating .env file" \
		cp .env.example .env; \
		exit 1; \
    fi
	@echo "🔍 Checking frontend/.env.production..."
	@if [ ! -f frontend/.env.production ]; then \
        echo "❌ frontend/.env.production not found!"; \
        echo "   Create it with: VITE_API_BASE_URL=http://YOUR_SERVER_IP/api/v1"; \
        exit 1; \
    fi
	@echo "🏗️ Building Docker images..."
	@docker compose -f docker-compose.dev.yml build
	@echo "📦 Installing backend dependencies (WITH dev packages for testing)..."
	@docker compose -f docker-compose.dev.yml run --rm -u "$(UID):$(GID)" app composer install --optimize-autoloader --no-interaction
	@echo "🔑 Generating application key..."
	@docker compose -f docker-compose.dev.yml run --rm app php artisan key:generate --force
	@echo "📦 Building frontend..."
	@docker run --rm -v "$(PWD)/frontend:/app" -w /app node:20-alpine sh -c "npm install && npm run build"
	@echo "📁 Copying frontend build to public directory..."
	@mkdir -p ./public/app
	@cp -r ./frontend/dist/* ./public/app/
	@echo "🚀 Starting services..."
	@docker compose -f docker-compose.dev.yml up -d
	@echo "⏳ Waiting for database to be ready..."
	@sleep 15
	@echo "🔧 Fixing permissions inside container..."
	@docker compose -f docker-compose.dev.yml exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
	@docker compose -f docker-compose.dev.yml exec app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
	@echo "🗄️ Running database migrations and seeders..."
	@docker compose -f docker-compose.dev.yml exec app php artisan migrate --seed --force
	@echo "🔗 Creating storage link..."
	@docker compose -f docker-compose.dev.yml exec app php artisan storage:link || true
	@echo "⚡ Caching configuration..."
	@docker compose -f docker-compose.dev.yml exec app php artisan config:cache
	@docker compose -f docker-compose.dev.yml exec app php artisan route:cache
	@docker compose -f docker-compose.dev.yml exec app php artisan view:cache
	@echo ""
	@echo "✅ Server development environment ready!"
	@echo "🌐 Access at: http://$$(curl -s ifconfig.me 2>/dev/null || echo 'YOUR_SERVER_IP')"

# Deploy/Update development
deploy-dev:
	@echo "🚀 Deploying updates to development..."
	@echo "🛑 Putting application in maintenance mode..."
	@docker compose -f docker-compose.dev.yml exec app php artisan down
	@echo "📥 Pulling latest changes..."
	@git pull origin develop
	@echo "🏗️ Rebuilding images..."
	@docker compose -f docker-compose.dev.yml build
	@echo "📦 Updating dependencies..."
	@docker compose -f docker-compose.dev.yml run --rm -u "$(UID):$(GID)" app composer install --no-dev --optimize-autoloader --no-interaction
	@echo "🗄️ Running migrations..."
	@docker compose -f docker-compose.dev.yml exec app php artisan migrate --force
	@echo "📦 Rebuilding frontend..."
	@docker run --rm -u "$(UID):$(GID)" -v "$(PWD)/frontend:/app" -w /app node:20-alpine sh -c "npm install && npm run build"
	@mkdir -p ./public/app
	@cp -r ./frontend/dist/* ./public/app/
	@echo "⚡ Optimizing..."
	@docker compose -f docker-compose.dev.yml exec app php artisan config:cache
	@docker compose -f docker-compose.dev.yml exec app php artisan route:cache
	@docker compose -f docker-compose.dev.yml exec app php artisan view:cache
	@echo "🔄 Restarting services..."
	@docker compose -f docker-compose.dev.yml restart app
	@echo "✅ Bringing application back online..."
	@docker compose -f docker-compose.dev.yml exec app php artisan up
	@echo "🎉 Deployment completed!"

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

logs-prod:
	@docker compose -f docker-compose.prod.yml logs -f

status-prod:
	@docker compose -f docker-compose.prod.yml ps
