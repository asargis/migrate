## Запуск проекта

Для того, чтобы запустить проект, нужно выполнить следующие шаги:

- Нужно установить и настроить БД (Postgres), php, nginx, composer.
- Затем скачать проект, используя команду <b>git clone https://github.com/asargis/migrate.git</b>
- После того, как скачается проект, нужно перейти в директорию с проектом и выполнить команду <b>composer install</b>.
- В файле <b>.env</b> нужно задать параметры подключения к БД.
- Убедившись, что все настроено и готово к запуску, переходим в корневую директорию проекта и выполняем следующую команду:
    <b>./artisan migrate:data полный_путь_к_входному_файлу полный_путь_к_выходному_файлу</b>
    
## Пример команды
./artisan migrate:data /home/sargis/random.csv /home/sargis/inv.csv
 
