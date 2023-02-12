Исходное положение:
1. Имеем 3 модели двух доменов: Goods и Orders
2. Имеем миграции, создающие соответствующие таблицы моделям
3. Good - товары на сайте, которые можно купить
4. Order - корзина заказа
5. Order Item - товар, лежащий в корзине. Связующая таблица между Order и Good
6. Документация написана на swagger:
   1. Перед началом работы разово необходимо выполнить php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
   2. После добавления правок в swagger необходимо вызвать php artisan l5-swagger:generate
   3. Документация доступна по адресу: http://localhost/api/documentation

Развернуть приложение:
1. Склонировать
2. Запустить Docker: ./vendor/bin/sail up
3. Выполнить миграции: php artisan migrate
4. Создать swagger-документацию: php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
5. Открыть localhost
6. Убедиться, что всё работает:
   1. http://localhost/api/goods - вернёт 3 товара;
   2. php artisan test - успешный тест
7. Прийти на собеседование

