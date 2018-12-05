[![Build Status](https://travis-ci.org/ComuneFI/BiCoreTemplate.svg?branch=master)](https://travis-ci.org/ComuneFI/BiCoreTemplate)

# BiCoreTemplate

## Intro

BiCoreTemplate è un template pronto all'uso che si poggia sul <a href="https://github.com/ComuneFI/BiCoreBundle" target="_blank">bundle</a> BiCoreBundle 

## Per iniziare

**NOTA**: richiede PHP 7.2.

### Prerequisiti

Testato su Debian next stable 10 (buster).

```sh
sudo apt install php-sqlite3 php-xml php-gd php-curl php-mbstring php-zip composer git
```

### Configurazione

Se necessario, modificare il file `.env` per impostare il database da utilizzare.
Di base usa sqlite3, se si cambia database è necessario installare il driver PHP corrispondente.

### Installazione

```
git clone https://github.com/ComuneFi/BiCoreTemplate.git
cd BiCoreTemplate
composer install
bin/console cache:clear
bin/console bicorebundle:dropdatabase --force
bin/console bicorebundle:install admin admin admin@admin.it
```

### Utilizzo

Avviare il server col comando:
```
bin/console server:start
```
oppure:
```
bin/console server:start 0.0.0.0:8000
```
se è installato in un container/docker e si desidera accedervi dall'host.

Visitare: http://127.0.0.1:8000/ ed effettuare il login (adnin/admin).

Il server rimane attivo in background. Per arrestarlo:
```
bin/console server:stop
```
