.PHONY: help install update backup seed reset

# Default target
help:
	@echo "DUCT-CENP Makefile Commands:"
	@echo "  make install   - Initial setup (build containers, generate key, migrate, seed)"
	@echo "  make update    - Pull latest code and rebuild the app container"
	@echo "  make backup    - Create a database backup in the backups/ directory"
	@echo "  make seed      - Run database seeders and generate Filament Shield permissions"
	@echo "  make reset     - WARNING: Destroy all containers and volumes, then start fresh"

install:
	@echo "==> Installing DUCT-CENP..."
	@if [ ! -f .env ]; then \
		cp .env.example .env; \
		echo "Created .env file. Please update it with your passwords, then run 'make install' again."; \
		exit 1; \
	fi
	docker compose up -d --build
	@echo "==> Waiting 15 seconds for database to initialize..."
	@sleep 15
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate --force
	docker compose exec app php artisan db:seed --force
	docker compose exec app php artisan shield:generate --all
	docker compose exec app php artisan shield:super-admin --user=1
	docker compose exec app php artisan storage:link
	@echo "==> Installation complete! The app is now live."

update:
	@bash backup.sh
	@echo "==> Updating DUCT-CENP..."
	git pull
	docker compose up -d --build app
	docker compose exec app php artisan migrate --force
	docker compose exec app php artisan storage:link
	@bash notify_update.sh
	@echo "==> Update complete!"

backup:
	@bash backup.sh

seed:
	@echo "==> Seeding database..."
	docker compose exec app php artisan db:seed --force
	docker compose exec app php artisan shield:generate --all
	@echo "==> Assigning super_admin role to User ID 1..."
	docker compose exec app php artisan shield:super-admin --user=1
	@echo "==> Seeding complete!"

reset:
	@echo "==> WARNING: This will destroy all data and reset the containers!"
	@read -p "Are you sure you want to reset? [y/N] " ans; \
	if [ "$$ans" = "y" ] || [ "$$ans" = "Y" ]; then \
		echo "==> Destroying containers and volumes..."; \
		docker compose down -v; \
		echo "==> Starting fresh..."; \
		docker compose up -d --build; \
		echo "==> Waiting 15 seconds for database to initialize..."; \
		sleep 15; \
		docker compose exec app php artisan migrate:fresh --seed --force; \
		docker compose exec app php artisan shield:generate --all; \
		docker compose exec app php artisan shield:super-admin --user=1; \
		docker compose exec app php artisan storage:link; \
		echo "==> Reset complete!"; \
	else \
		echo "==> Reset aborted."; \
	fi
