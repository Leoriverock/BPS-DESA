FROM nexus.bps.net:5001/vtiger-base:latest


COPY ci/docker/php.ini /usr/local/etc/php/
COPY ci/docker/vtiger.json ci/docker/.symvol /usr/src/vtiger/
COPY ci/docker/vtiger-ssl.* /etc/apache2/ssl/
COPY ci/docker/000-default.conf /etc/apache2/sites-available/

COPY . /var/www/html/
COPY ci/docker/.symvol /var/www/html/
COPY ci/docker/config.inc.php ci/docker/config.performance.php ci/docker/loading.php /var/www/html/
COPY ci/docker/vtiger-*.sh /usr/local/bin/
COPY ci/docker/vtiger-*.php /usr/src/vtiger/
RUN mkdir -p /var/www/html/test /var/www/html/user_privileges /var/www/html/storage

RUN rm -rf /var/www/html/docker
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    a2enmod ssl && a2enmod rewrite && \
    cd /usr/src/vtiger && \
    vtiger permissions --fix && \
    mkdir -p volume /var/lib/vtiger && \
    apt-get clean && rm -rf /tmp/* /var/tmp/* /var/lib/apt/lists/*


COPY ci/docker/LoggerManager.php /var/www/html/libraries/log4php/
COPY ci/docker/crontab /etc/

VOLUME ["/var/lib/vtiger"]

WORKDIR /app

##ENV VT_ADMIN_USER="admin" \
#    VT_ADMIN_PASSWORD="admin" \
#    VT_ADMIN_EMAIL="jaramburu@jeancito.com" \
#    VT_CURRENCY_NAME="Uruguay, Pesos" \
#    MYSQL_HOST="mysql" \
#    MYSQL_DATABASE="vtiger"


CMD ["vtiger-foreground.sh"]

