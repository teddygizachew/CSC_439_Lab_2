FROM php:7.4-cli

#set our working directory in the container
WORKDIR /usr/src/myapp

#install php extensions
RUN pecl install xdebug && docker-php-ext-enable xdebug && echo "xdebug.mode=coverage" >> $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini

#install phpunit
COPY phpunit-9.5.23.phar /usr/local/bin/phpunit
RUN chmod +x /usr/local/bin/phpunit
COPY phpunit.xml ./phpunit.xml

#copy our code into the container
COPY . /usr/src/myapp

CMD [ "php", "./Main.php" ]