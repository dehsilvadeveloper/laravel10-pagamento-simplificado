## Utilizando a API

### Utilizando os endpoints da API

Para utilizar os endpoints da API você irá precisar de um programa que possa efetuar requisições http. Você pode usar o **Postman** ou o **Insomnia**, por exemplo.

[Postman](https://www.postman.com/downloads/)

[Insomnia](https://insomnia.rest/download)

### Documentação da API

A aplicação fornece uma página gerada com o package **Scribe** que contém detalhes sobre todos os *endpoints da API*.

Para acessar a página você deve utilizar a seguinte url:

```
http://localhost:9999/api-docs
```

Lembre-se de iniciar a aplicação antes de tentar acessar a página da documentação da API.

### Filas / Queues

A aplicação conta com ações *assíncronas*, ou seja, ações que não são executadas no momento da requisição. As ações são as seguintes:

- Disparo de notificação de boas-vindas (após criação de usuário);
- Disparo de notificação de transferência recebida (após um usuário receber uma nova transferência);

Estas ações são armazenadas no **Redis** e ficam aguardando disponibilidade da aplicação para executá-las.

Para executar estas ações você deve rodar o seguinte comando no terminal:

```
docker-compose exec main php artisan queue:work --queue=notifications
```

Este comando vai deixar a aplicação em estado de espera, ou seja, aguardando que ações sejam colocadas na fila para serem executadas.

Se você deseja interromper o estado de espera da aplicação, basta utilizar a seguinte combinação de teclas no terminal:

`ctrl + c`

### Rodando testes

Para rodar os testes da aplicação, você pode utilizar o seguinte comando num terminal aberto na raiz do diretório do projeto:

```
docker-compose exec main php artisan test
```

Se preferir você pode entrar no container *main* utilizando o comando a seguir:

```
docker-compose exec -it main bash
```

E depois disso você pode executar os testes utilizando o *Artisan*, que fornece *reports* mais detalhados.

```
php artisan test
```

Também existe a possibilidade de rodar os testes utilizando diretamente o *PHPUnit*.

```
./vendor/bin/phpunit
```

Quaisquer opções de execução que queira utilizar no *PHPUnit* também valem para o *Artisan*.

Exemplo:

```
// Interromper a execução após primeira falha

php artisan test --stop-on-failure
ou
./vendor/bin/phpunit --stop-on-failure
```
