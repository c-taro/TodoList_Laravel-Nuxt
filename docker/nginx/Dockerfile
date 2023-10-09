FROM nginx:1.20-alpine

ENV TZ Asia/Tokyo

COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

RUN ln -sf /dev/stdout /var/log/nginx/access.log
RUN ln -sf /dev/stderr /var/log/nginx/error.log