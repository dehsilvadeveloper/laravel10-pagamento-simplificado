# Resolvendo o desafio

### Banco de dados e arquitetura

No quesito banco de dados foi escolhido para a persistência dos dados requeridos pela aplicação o uso do **MySQL**, que é um banco de dados relacional com o qual já tenho familiaridade.

Para a construção da API foi utilizado o **Laravel**, um framework *open source* de **PHP** cujo objetivo é permitir que o desenvolvedor trabalhe de forma mais rápida e eficaz, com um foco maior no desenvolvimento das regras de negócio e menor em questões de arquitetura e configurações. Dentre as *features* do framework utilizadas podemos citar as *classes requests*, que realizam validação de dados recebidos, e os models construídos com *Eloquent*, um poderoso ORM (Object Relational Mapper) que já vêm embutido no Laravel por padrão.

Com relação a arquitetura foi decidido efetuar uma distribuição do código em diferentes pastas de domínio, uma ideia inspirada em conceitos do DDD (Domain Driven Design). Desta maneira foram gerados pastas de domínio para "transfer", "common" e "user", por exemplo, e a lógica do negócio foi dividida entre elas objetivando uma melhor organização. 

Além disso também foram adotados os padrões de projeto **service pattern** e **repository pattern** com o intuito de ter uma divisão entre as camadas de regra de negócio, de manipulação de dados e de apresentação de dados, permitindo uma maior separação de responsabilidades e facilitando a reutilização de trechos de códigos, respeitando, desta forma, a abordagem do DRY (Don’t Repeat Yourself).

### Diagrama de fluxo

Foi desenhado um diagrama de fluxo para a aplicação usando a ferramenta [Draw.io](https://app.diagrams.net/).

<a href="./diagrams/pagamento_simplificado.drawio.png" target="_blank">
    <img src="./diagrams/pagamento_simplificado.drawio.png" alt="Diagrama de Fluxo" width="150" />
</a>

### Explicação da API

Breve.

### Segurança

Com relação a segurança da API, foi utilizada uma proteção das rotas com a exigência de envio de um token de acesso no cabeçalho das requisições. A aplicação fornece um usuário padrão para uso e uma rota para geração de tokens. Detalhes sobre esta rota podem ser obtidos na *documentação da API*.

Embora não tenha sido solicitado nada nesse sentido na descrição do desafio, foi considerado válido incluir algum tipo de proteção para a API.

### Stubs customizados

Como um extra do projeto foram implementados arquivos *stubs* para criar classes do tipo *service*, *repository* e *DTO*. Você pode utilizá-los durante desenvolvimento a partir de comandos no *artisan*, conforme exemplos a seguir:

Criando classe service:

```
php artisan make:service UserService
ou
php artisan make:service Domain/User/Services/UserService
```

Criando classe repository:

```
php artisan make:repository UserRepository
ou
php artisan make:repository Domain/User/Repositories/UserRepository
```

Criando classe DTO (Data Transfer Object):

```
php artisan make:dto CreateUserDto
ou
php artisan make:dto Domain/User/DataTransferObjects/CreateUserDto
```

As classes DTO fazem uso do package **laravel-data**.

### Ambiente

Visando evitar problemas de divergência de ambientes em diferentes máquinas, todo o código da aplicação foi incluído em containers criados com a ferramenta **Docker**.

### Testes

Os testes da aplicação, importantes para controle de comportamentos esperados e detecção de falhas de fluxo, foram construídos usando o *PHPUnit*. Caso queira saber mais detalhes sobre como executá-los, você pode se referir ao tópico **Rodando Testes** na seguinte [página](using_api.md).

### Documentação da API

Para a criação da documentação da API foi utilizado o [Scribe](https://scribe.knuckles.wtf/laravel/), um package especializado em gerar documentações com uma linguagem mais humanizada a partir do código-fonte de aplicações *Laravel* ou *Lumen*.

Você pode acessar a documentação da API através da seguinte URL:

```
http://localhost:9999/api-docs
```

Lembre-se de iniciar a aplicação antes de tentar acessar a página da documentação da API.