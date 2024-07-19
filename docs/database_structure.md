## Estrutura do banco de dados

Neste documento você pode conferir a estrutura proposta de tabelas do banco de dados da aplicação.

#### Tabela: api_users

Tabela com informações de usuários da API (utilizadores externos, etc).

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| name | varchar(70) | sim | Nome do usuário de API |
| email | varchar(70) | não | Endereço de e-mail do usuário de API |
| password | varchar(255) | Não | Senha encriptada do usuário de API |
| created_at | datetime | não | Data e hora da criação do usuário de API |
| updated_at | datetime | sim | Data e hora da última atualização do usuário de API |

#### Tabela: document_types

Tabela com os tipos de documentos possíveis para os usuários (cpf, cnpj).

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| name | varchar(50) | não | Nome do tipo de documento |
| created_at | datetime | não | Data e hora da criação do tipo de documento |
| updated_at | datetime | sim | Data e hora da última atualização do tipo de documento |

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
| recipient_id | integer | não | ID de usuário de quem deve receber a notificação |
| type | text | não | Nome da classe de notificação disparada (WelcomeNotification, etc) |
| channel | varchar(70) | não | Canal pelo qual foi disparada a notificação (mail, sms, etc) |
| response | text | não | Resposta do envio da notificação |
| created_at | datetime | não | Data e hora da criação da notificação |
| updated_at | datetime | sim | Data e hora da última atualização da notificação |

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

Tabela com informações dos usuários.

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| name | varchar(70) | não | Nome completo do usuário |
| user_type_id | integer | não | ID do tipo de usuário |
| document_number | varchar(14) | não | Número de documento do usuário |
| document_type_id | integer | não | ID do tipo de documento do usuário |
| email | varchar(70) | não | Endereço de e-mail do usuário |
| password | varchar(255) | Não | Senha encriptada do usuário |
| created_at | datetime | não | Data e hora da criação do usuário |
| updated_at | datetime | sim | Data e hora da última atualização do usuário |
| deleted_at | datetime | sim | Data e hora em que o usuário foi removido (soft delete) |

#### Tabela: user_types

Tabela com os tipos de usuários possíveis (comum, lojista).

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| name | varchar(50) | não | Nome do tipo de usuário |
| created_at | datetime | não | Data e hora da criação do tipo de usuário |
| updated_at | datetime | sim | Data e hora da última atualização do tipo de usuário |

#### Tabela: wallets

Tabela com informações das carteiras dos usuários.

| Campo | Tipo | Nullable | Descrição |
|-|-|-|-|  
| id | integer | não | Chave primária | 
| user_id | integer | não | ID do usuário a quem pertence a carteira |
| balance | decimal(12,2) | não | Saldo atual da carteira |