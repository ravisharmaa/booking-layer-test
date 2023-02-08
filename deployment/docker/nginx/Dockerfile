FROM nginx:1.21-alpine

ARG FPM_HOST="127.0.0.1"

RUN echo "$FPM_HOST"

COPY ./deployment/docker/nginx/nginx.conf  /etc/nginx/
COPY ./deployment/docker/nginx/templates /etc/nginx/templates/
# RUN echo "upstream php-upstream { server ${FPM_HOST}:9000; }" > /etc/nginx/conf.d/upstream.conf

RUN sed -i "s/fastcgi_pass_network_address/${FPM_HOST}/g" /etc/nginx/templates/default.conf.template

WORKDIR /var/www/public
COPY ./public .

EXPOSE 80
EXPOSE 443
