## Instruções para containers Docker

### Acessando containers

Para efetuar o acesso a containers, você pode utilizar o seguinte comando:

```
docker-compose exec -it <nome do serviço> <comando que você deseja executar no container>
```

Exemplo:

```
docker-compose exec -it main bash
```

Vale ressaltar que o nome do serviço é definido no arquivo **docker-compose.yml**.

Para sair de um container, basta utilizar o seguinte comando:

```
exit
```

#### Acessando container "main"

Para acessar o container **main** você pode utilizar o comando a seguir:

```
docker-compose exec -it main bash
```

Uma vez dentro do container você poderá rodar comandos que julgue necessário, como comandos **artisan**, por exemplo.

Exemplo:

```
php artisan db:seed
```

#### Acessando container "mysql"

Para acessar o container **mysql** você pode utilizar o comando a seguir:

```
docker-compose exec -it mysql mysql -u root -p root
```

As opções `-u` e `-p` se referem a **usuário** e **senha** respectivamente e esses valores estão presentes nas seguintes variáveis de ambiente do arquivo **.env**: *DB_USERNAME* e *DB_PASSWORD*.

Depois disso você estará apto a executar queries SQL que forem necessárias. 

Você pode, por exemplo, listar todos os databases criados.

```
SHOW DATABASES;
```

Ou até listar as tabelas que existem dentro de um database específico.

```
USE database_name; SHOW TABLES;
```

##### Conectando programas ao banco de dados dockerizado

Para conectar programas como o **MySQL Workbench** ou o **DBeaver** ao banco de dados da aplicação, você pode utilizar as seguintes configurações:

```
hostname: mysql
port: 3398
username: root
password: root
```

#### Acessando container "redis"

Para acessar o container **redis** você pode utilizar o comando a seguir:

```
docker-compose exec -it redis redis-cli
```

Uma vez dentro do container você poderá rodar comandos que julgue necessário.

Por exemplo, para selecionar o database desejado, você pode usar o comando:

```
SELECT 2
```

E então, para listar todas as chaves presentes neste database, você pode usar o comando:

```
KEYS *
```

### Reiniciando containers

Para reiniciar containers, você pode utilizar o seguinte comando:

```
docker-compose restart <nome do serviço>
```

Exemplo:

```
docker-compose restart redis
```

Se você não informar o nome do serviço, todos os containers serão reiniciados.

### Visualizando os logs de um container específico

Para visualizar os logs de um container específico, você pode utilizar o seguinte comando:

```
docker-compose logs <nome do serviço>
```

Exemplo:

```
docker-compose logs redis
```

Se você deseja seguir os logs em tempo real, utilize a opção `-f`:

```
docker-compose logs -f redis
```

Para limitar a quantidade de logs exibidos, você pode usar a opção `--tail`:

```
docker-compose logs --tail=50 redis
```
