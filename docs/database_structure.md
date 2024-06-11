## Estrutura do banco de dados

Neste documento você pode conferir a estrutura proposta de tabelas do banco de dados da aplicação.

#### Tabela: api_users

Tabela com informações de usuários da API (serviços externos, etc).

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| name | varchar(70) | sim | Nome do usuário |
| email | varchar(70) | não | Endereço de e-mail do usuário |
| password | varchar(255) | Não | Senha encriptada do usuário |
| created_at | datetime | não | Data e hora da criação do usuário |
| updated_at | datetime | sim | Data e hora da última atualização do usuário |

#### Tabela: customers

Tabela com informações dos clientes.

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária |
| user_id | integer | não | ID de usuário do cliente |
| customer_type_id | integer | não | ID do tipo de cliente |
| name | varchar(70) | não | Nome completo do cliente |
| document_number | varchar(14) | não | Número de documento do cliente |
| document_type_id | integer | não | ID do tipo de documento do cliente |
| created_at | datetime | não | Data e hora da criação do cliente |
| updated_at | datetime | sim | Data e hora da última atualização do cliente |

#### Tabela: customer_types

Tabela com os tipos de clientes possíveis (comum, lojista).

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| name | varchar(50) | não | Nome do tipo de cliente |

#### Tabela: document_types

Tabela com os tipos de documentos possíveis para os clientes (cpf, cnpj).

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| name | varchar(50) | não | Nome do tipo de documento |

#### Tabela: external_authorization_responses

Tabela com respostas recebidas por serviço de autorização externo antecedendo processamento de transferência.

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| transfer_id | integer | não | ID da transferência |
| response | json | não | Objeto JSON retornado como resposta do autorizador |
| created_at | datetime | não | Data e hora da criação da resposta recebida |
| updated_at | datetime | sim | Data e hora da última atualização da resposta recebida |

#### Tabela: failed_notifications

Tabela com informações de notificações que falharam no processo de envio.

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| notification_id | integer | não | ID da notificação |
| exception | text | não | Mensagem de erro obtida |
| failed_at | datetime | não | Data e hora da criação do registro da falha |

#### Tabela: notifications 

Tabela com as notificações disparadas pela aplicação.

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| message | text | não | Texto da notificação |
| recipient_id | integer | não | ID de usuário de quem deve receber a notificação |
| notification_type_id | integer | não | ID do tipo de notificação |
| notification_status_id | integer | não | ID do status da notificação |
| created_at | datetime | não | Data e hora da criação do cliente |
| updated_at | datetime | sim | Data e hora da última atualização do cliente |

#### Tabela: notification_status_histories

Tabela com as mudanças de status de uma notificação dentro do ciclo de vida da mesma na aplicação.

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária |
| notification_id | integer | não | ID da notificação |
| notification_status_id | integer | não | ID do tipo de status da notificação |
| changed_at | datetime | não | Data e hora da da mudança de status da notificação |

#### Tabela: notification_types

Tabela com o tipo de notificações que podem ser disparadas pela aplicação (email, sms, external_notifier).

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| name | varchar(50) | não | Nome do tipo de notificação |

#### Tabela: notification_statuses

Tabela com os statuses possíveis para as notificações (pendente, enviado, erro).

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| name | varchar(50) | não | Nome do status de notificação |

#### Tabela: transfers

Tabela com informações das transferências realizadas na aplicação.

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária |
| payer_id | integer | não | ID de usuário de quem enviou o dinheiro |
| payee_id | integer | não | ID de usuário de quem recebeu o dinheiro |
| amount | decimal(12,2) | não | Valor transferido |
| transfer_status_id | integer | não | ID do tipo de status da transferencia |
| created_at | datetime | não | Data e hora da criação da transferência |
| updated_at | datetime | sim | Data e hora da última atualização da transferência |

#### Tabela: transfer_status_histories

Tabela com as mudanças de status de uma transferência dentro do ciclo de vida da mesma na aplicação.

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária |
| transfer_id | integer | não | ID da transferencia |
| transfer_status_id | integer | não | ID do tipo de status da transferencia |
| changed_at | datetime | não | Data e hora da da mudança de status da transferência |

#### Tabela: transfer_statuses

Tabela com os statuses possíveis para as transferências (pendente, autorizado, concluído, erro).

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| name | varchar(50) | não | Nome do status de transferência |

#### Tabela: users

Tabela com informações das contas de usuário dos clientes.

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| name | varchar(70) | sim | Nome do usuário |
| email | varchar(70) | não | Endereço de e-mail do usuário |
| password | varchar(255) | Não | Senha encriptada do usuário |
| created_at | datetime | não | Data e hora da criação do usuário |
| updated_at | datetime | sim | Data e hora da última atualização do usuário |

#### Tabela: wallets

Tabela com informações das carteiras dos clientes.

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| user_id | integer | não | ID do usuário |
| balance | decimal(12,2) | não | Saldo atual da carteira |