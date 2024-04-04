FROM php:8-cli-alpine

RUN set -eux ; apk add --no-cache --virtual .neoscs-rundeps tini

ADD ./bin/neoscs.phar /usr/local/bin/neoscs
RUN chmod u+x /usr/local/bin/neoscs

COPY ./.docker/docker-entrypoint.sh /docker-entrypoint.sh
WORKDIR /app

ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["neoscs"]