### Примеры очередей на PHP
Сделано для собственного понимания работы RabbitMQ.
Большинство примеров взято с офф сайта [RabbitMQ](https://www.rabbitmq.com/getstarted.html)

## Установка

`make build` - собираем докер

`make install` - ставим зависимости

Сборку докера делал сам (опыта мало) и если есть ошибки все описал тут [docker](https://github.com/Drumsid/Docker-template).
Можно глянуть и по фиксить 

## Проверка работы сервисов

После установки можно проверить как подключились все сервисы. 
Проверяем в браузере [localhost:8082](http://localhost:8082/) и по ссылкам проверяем корректность работы сервисов

## Использование

В папке RabbitMQ лежат примеры. В каждой папке отдельный пример, в каждом файле вверху описана логика работы, запуск 
проверок и тп.