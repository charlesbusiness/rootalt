#!/bin/bash

# Update packages
sudo apt update && sudo apt upgrade -y

# Install PHP, PHP-FPM, and common extensions (adjust PHP version as needed)
sudo apt install -y php php-cli php-fpm php-common php-mbstring php-xml php-curl php-mysql php-bcmath php-zip unzip curl

# Install Composer globally
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Supervisor
sudo apt install -y supervisor

# Enable and restart PHP-FPM and Supervisor
sudo systemctl enable php-fpm
sudo systemctl restart php-fpm
sudo systemctl enable supervisor
sudo systemctl restart supervisor

echo "EC2 setup completed: PHP, Composer, and Supervisor are installed."
