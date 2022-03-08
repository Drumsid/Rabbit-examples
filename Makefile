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

#===================  docker simple =====================================
receive_s_1:
	docker exec -it rabbit-php-fpm  php RabbitMQ/simple_1/receive.php

sending_s_1:
	 docker exec -it rabbit-php-fpm  php RabbitMQ/simple_1/sending.php

#===================  my simple =====================================
#отправляем сообщение в очередь А или В
my_sending_simple:
	 docker exec -it rabbit-php-fpm  php RabbitMQ/mySimple_1/sending.php

#получаем сообщение из очереди А
receive_A:
	 docker exec -it rabbit-php-fpm  php RabbitMQ/mySimple_1/receiveA.php

#получаем сообщение из очереди В
receive_B:
	 docker exec -it rabbit-php-fpm  php RabbitMQ/mySimple_1/receiveB.php