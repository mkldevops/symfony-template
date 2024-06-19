FROM gitpod/workspace-full:latest

#RUN sudo apt update
#RUN sudo apt install -y apt-utils apt-transport-https postgresql postgresql-contrib
#RUN sudo install-packages php-intl php-redis php-amqp php-pgsql

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash
RUN sudo apt install symfony-cli

RUN curl "https://castor.jolicode.com/install" | bash \
    && sudo mv castor.phar /usr/local/bin/castor \
    && sudo chmod +x /usr/local/bin/castor

