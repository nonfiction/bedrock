# MySQL client to create users & databases
FROM mysql:latest

RUN set -ex; \
  apt-get update; \
  apt-get install -y --no-install-recommends \
    less \
    curl \
    unzip \
    rsync \
    openssh-client \
    ca-certificates \
    dialog \
    ruby; \
  update-ca-certificates \
  ;

# Thor
RUN gem install thor dotenv
COPY ./Thorfile /bin/tasks
RUN chmod +x /bin/tasks

WORKDIR /srv/

RUN touch /.env

ENTRYPOINT ["tasks"]

CMD ["list"]
