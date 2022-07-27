FROM debian:jessie-slim

ENV DEBIAN_FRONTEND=noninteractive

WORKDIR /var/www/ojs

RUN apt-get update && apt-get install -y patch php5-cli php5-common \
    phpunit php5-adodb php5-intl php5-mysql php5-readline php5-json --no-install-recommends && rm -rf /var/lib/apt/lists/*


COPY . .

RUN cp config.TEMPLATE.inc.php config.inc.php && mkdir -p /ojs/files

# Added configuration of smtp server mailhog (/var/www/ojs/config.inc.php)
RUN sed -i 's/\; smtp = On/smtp = On/g' config.inc.php \
    && sed -i "s/\; smtp_server = mail.example.com/smtp_server = mailhog/g" config.inc.php \
    && sed -i "s/\; smtp_port = 25/smtp_port = $SMTP_MAILHOG_PORT/g" config.inc.php ;
# RUN sudo chwon root:root -R "./tools/tools-lepidus"
# RUN sudo chmod 777 -R "./tools/tools-lepidus"
CMD ["php", "-S", "0.0.0.0:8000"]