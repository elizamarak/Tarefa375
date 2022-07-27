FROM debian:buster-slim

ENV DEBIAN_FRONTEND=noninteractive

WORKDIR /var/www/ojs

RUN apt-get update && apt-get install -y patch php7.3-cli php7.3-common composer \
    phpunit php7.3-xml php7.3-mbstring php7.3-intl php7.3-mysql php7.3-readline php7.3-json --no-install-recommends && rm -rf /var/lib/apt/lists/*


COPY . .

RUN mkdir -p /ojs/files

# Added configuration of smtp server mailhog (/var/www/ojs/config.inc.php)
RUN sed -i 's/\; smtp = On/smtp = On/g' config.inc.php \
    && sed -i "s/\; smtp_server = mail.example.com/smtp_server = mailhog/g" config.inc.php \
    && sed -i "s/\; smtp_port = 25/smtp_port = $SMTP_MAILHOG_PORT/g" config.inc.php ;
# RUN sudo chwon root:root -R "./tools/tools-lepidus"
# RUN sudo chmod 777 -R "./tools/tools-lepidus"
CMD ["php", "-S", "0.0.0.0:8000"]
