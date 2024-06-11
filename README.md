[![Laravel][laravel-shield]][ref-laravel]
[![PHP][php-shield]][ref-php]
[![MySQL][mysql-shield]][ref-mysql]
[![JWT][jwt-shield]][ref-jwt]
[![Composer][composer-shield]][ref-composer]
[![Docker][docker-shield]][ref-docker]
[![Git][git-shield]][ref-git]
[![Redis][redis-shield]][ref-redis]

# Desafio Pagamento Simplificado (Versão Laravel 10)

## Descrição do desafio

O objetivo deste desafio é construir uma API RESTFul para efetuar pagamentos simples. Nela deve ser possível realizar transferências de dinheiro entre usuários. Temos 2 tipos de usuários: os *comuns* e os *lojistas*, sendo que ambos possuem carteira com dinheiro e realizam transferências entre eles.

### Requisitos

A API desenvolvida deve respeitar as regras de negócio a seguir:

- Este serviço deve ser RESTFul;

- Para ambos tipos de usuário, precisamos do `Nome Completo`, `CPF`, `e-mail` e `Senha`. CPF/CNPJ e e-mail devem ser únicos no sistema. Sendo assim, seu sistema deve permitir apenas um cadastro com o mesmo CPF ou endereço de e-mail;

- Usuários comuns podem enviar dinheiro (efetuar transferência) para lojistas e entre usuários comuns;

- Lojistas só recebem transferências, não enviam dinheiro para ninguém;

- Validar se o usuário tem saldo antes da transferência;

- Antes de finalizar a transferência, deve-se consultar um *serviço autorizador externo*. Use este **mock** [https://util.devi.tools/api/v2/authorize](https://util.devi.tools/api/v2/authorize) para simular o serviço utilizando o verbo `GET`. Ele retornará uma resposta no seguinte formato:

```json
{"status" : "success", "data" : { "authorization" : true }}
```

A resposta retornada pelo mock é randômica, então também vai acontecer de serem retornadas respostas negativas.

```json
{"status" : "fail", "data" : { "authorization" : false }}
```

Lembre-se considerar as possíveis respostas que podem ser recebidas do mock no desenvolvimento da API;

- A operação de transferência deve ser uma *transação* (ou seja, revertida em qualquer caso de inconsistência) e o dinheiro deve voltar para a carteira do usuário que envia (independente do tipo);

- No recebimento de pagamento, o usuário comum ou lojista precisa receber uma *notificação* (envio de email, sms, etc) enviada por um serviço de terceiro e eventualmente este serviço pode estar indisponível/instável. Use este **mock** [https://util.devi.tools/api/v1/notify](https://util.devi.tools/api/v1/notify) para simular o envio da notificação utilizando o verbo `POST`. Ele retornará uma resposta no seguinte formato:

```json
{"status" : "success", "data" : { "sent" : true }}
```

A resposta retornada pelo mock é randômica, então também vai acontecer de serem retornadas respostas negativas.

```json
{"status" : "fail", "data" : { "sent" : false }}
```

Lembre-se considerar as possíveis respostas que podem ser recebidas do mock no desenvolvimento da API.

### Endpoint de transferência

Você pode implementar o que achar conveniente para o funcionamento do sistema, porém a avaliação do resultado do desafio vai considerar apenas o fluxo de transferência entre 2 usuários. A implementação do endpoint de transferência deve seguir o contrato seguir:

```json
POST /transfer
Content-Type: application/json

{
  "value": 100.0,
  "payer": 4,
  "payee": 15
}
```

Lembre-se de respeitar as regras de negócio do desafio.

## Desenvolvido com

| Nome       | Versão  |
| ---------- | -------- |
| Laravel | v10.x + |
| PHP | v8.2.x + |
| Docker | v20.10.x + |
| Docker Compose | v3.8.x + |
| MySQL | v8.0.x |
| Redis | v6.2.x |

## Documentação

* [Resolvendo o desafio](./docs/answering_challenge.md)
* [Como começar](./docs/getting_started.md)
* [Instruções para containers Docker](./docs/docker_containers_instructions.md)
* [Utilizando a API](./docs/using_api.md)
* [Estrutura do banco de dados](./docs/database_structure.md)

<!-- Badge Shields -->
[laravel-shield]: https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white
[php-shield]: https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white
[mysql-shield]: https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white
[jwt-shield]: https://img.shields.io/badge/JWT-black?style=for-the-badge&logo=JSON%20web%20tokens
[composer-shield]: https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white
[docker-shield]: https://img.shields.io/badge/docker-%230db7ed.svg?style=for-the-badge&logo=docker&logoColor=white
[git-shield]: https://img.shields.io/badge/git-%23F05033.svg?style=for-the-badge&logo=git&logoColor=white
[redis-shield]: https://img.shields.io/badge/Redis-DC382D?style=for-the-badge&logo=redis&logoColor=white

<!-- References -->
[ref-laravel]: https://laravel.com/docs/10.x/readme
[ref-php]: https://www.php.net
[ref-mysql]: https://www.mysql.com
[ref-jwt]: https://jwt.io
[ref-composer]: https://getcomposer.org
[ref-docker]: https://www.docker.com
[ref-git]: https://git-scm.com
[ref-redis]: https://redis.io/docs/latest/develop/data-types/