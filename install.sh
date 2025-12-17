#!/bin/bash

# Laravel API Starter Pack Installation Script
# This script will help you set up a new Laravel project with the starter pack

echo "================================================"
echo "  Laravel API Starter Pack - Installation"
echo "================================================"
echo ""

# Check if project name is provided
if [ -z "$1" ]; then
    echo "❌ Error: Project name is required"
    echo "Usage: ./install.sh project-name"
    exit 1
fi

PROJECT_NAME=$1

echo "📦 Creating new Laravel project: $PROJECT_NAME"
composer create-project laravel/laravel $PROJECT_NAME

if [ ! -d "$PROJECT_NAME" ]; then
    echo "❌ Error: Failed to create Laravel project"
    exit 1
fi

cd $PROJECT_NAME

echo ""
echo "📥 Installing dependencies..."
composer require laravel/sanctum
composer require spatie/laravel-permission

echo ""
echo "📁 Copying starter pack files..."

# Create directories
mkdir -p app/{Traits,Helpers,Interfaces,Repositories}

# Copy files
echo "Copying app files..."
cp -r ../app/* app/

echo "Copying database files..."
cp -r ../database/* database/

echo "Copying routes..."
cp ../routes/api.php routes/

echo "Copying bootstrap..."
cp ../bootstrap/app.php bootstrap/
cp ../bootstrap/providers.php bootstrap/

echo "Copying config..."
cp ../config/*.php config/

echo ""
echo "🗄️  Setting up database..."

# Update .env for SQLite
sed -i.bak 's/DB_CONNECTION=sqlite/DB_CONNECTION=sqlite/' .env
sed -i.bak 's/# DB_DATABASE=/DB_DATABASE=/' .env

echo ""
echo "🔑 Generating application key..."
php artisan key:generate

echo ""
echo "📊 Running migrations and seeders..."
touch database/database.sqlite
php artisan migrate:fresh --seed

echo ""
echo "✅ Installation completed!"
echo ""
echo "================================================"
echo "  Next Steps:"
echo "================================================"
echo "1. cd $PROJECT_NAME"
echo "2. php artisan serve"
echo "3. Test the API with the credentials:"
echo "   Email: admin@example.com"
echo "   Password: password"
echo ""
echo "📖 Read the README.md for more information"
echo "================================================"
