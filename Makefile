#запуск докер приложений
build:
	docker-compose up --build -d

#остановка докер приложений
stop:
	docker-compose down

all-stop:
	docker stop $(docker container ls -qa)

#запуск композера внутри докера
install:
	docker exec -it rabbit-php-fpm composer install

#заходим в bash
bash:
	docker exec -it rabbit-php-fpm bash

receive_s_1:
	docker exec -it rabbit-php-fpm  php RabbitMQ/simple_1/receive.php

sending_s_1:
	 docker exec -it rabbit-php-fpm  php RabbitMQ/simple_1/sending.php