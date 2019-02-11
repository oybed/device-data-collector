FROM centos:7

MAINTAINER Øystein Bedin <oybed@hotmail.com>

ENV LIBMBUS_VER 0.8.0
ENV LIBMBUS_URL http://www.rscada.se/public-dist/libmbus-${LIBMBUS_VER}.tar.gz

RUN yum -y update; \
    yum install -y php cronie gcc make zip

RUN curl -o /tmp/libmbus.tar.gz ${LIBMBUS_URL}; \
    tar -xzf /tmp/libmbus.tar.gz; \
    cd libmbus-${LIBMBUS_VER}; \
    ./configure; \
    make; \
    make install

ADD device-collection-cron /etc/cron.d/

RUN touch /var/log/cron.log; \
    crontab /etc/cron.d/device-collection-cron

COPY bin /usr/local/bin

USER ${USER_UID}

CMD [ "/usr/local/bin/run" ]

