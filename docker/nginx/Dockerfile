FROM nginx:1.20-alpine
LABEL maintainer="Nozomi Nakamura <itijiku.cathy@gmail.com>"

RUN apk add tzdata
ENV TZ=Asia/Tokyo

COPY ./docker/nginx/conf.d/*.conf /etc/nginx/conf.d/

WORKDIR /var/www/html