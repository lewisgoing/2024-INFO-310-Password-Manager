FROM nginx:latest as router

ADD ./nginx-default.conf /etc/nginx/conf.d/default.conf
ADD ./webapp/public /var/www/html/public