# tarefa-375
 https://pkp.sfu.ca/ojs/ojs_download/  
Repositório de códigos para tarefa:
documentacao-e-tarefas/desenvolvimento_e_infra#375

Esse repositório contem 3 diretórios:

files-backups  
ojs-2.4.8-5
ojs-3.2.1-4

|usuário|senha|
| ---- | ---- |
|root|123456|
|autor|123456|
|editor|123456|

## Requisitos

docker e docker-compose

``` bash
sudo apt update && sudo apt install -y docker.io docker-compose
```

## Instalação

1. Clone o repositório  
``` bash
git clone git@gitlab.lepidus.com.br:luisfelipe/tarefa-375.git
```
2. Entre no diretório ojs-2.4.8-5
``` bash
cd tarefa-375/ojs-2.4.8-5
```
## Uso


### Backup
docker exec CONTAINER mysqldump -u root --password=root ojs > backup.sql

### Restore
cat backup.sql | docker exec -i CONTAINER mysql -u root --password=root ojs
