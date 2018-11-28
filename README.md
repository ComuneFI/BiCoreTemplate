BiCoreTemplate
=============
[![Build Status](https://travis-ci.org/ComuneFI/BiCoreTemplate.svg?branch=master)](https://travis-ci.org/ComuneFI/BiCoreTemplate)

Intro:
-------------
BiCoreTemplate è un template pronto all'uso che si poggia sul <a href="https://github.com/ComuneFI/BiCoreBundle" target="_blank">bundle</a> BiCoreBundle 

Installazione:
-------------

```
    git clone https://github.com/ComuneFi/BiCoreTemplate.git
    cd BiCoreTemplate
    #Modificare il file .env per impostare il database da utilizzare
    composer install
    bin/console cache:clear
    bin/console bicorebundle:dropdatabase --force
    bin/console bicorebundle:install admin admin admin@admin.it
    
    ##Start server 
    bin/console server:start 2>&1 &
    
    http://127.0.0.1:8000/