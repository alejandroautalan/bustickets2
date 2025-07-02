# bustickets

Comandos utiles despues de clonar:

    # Instalar vendors javascrip/css
    php bin/console importmap:install
    
    # En producción:
    php bin/console asset-map:compile

Backup database:

    mysqldump --skip-ssl -u admin -p -h 192.168.122.11 bustickets2 > bustickets-02.sql

