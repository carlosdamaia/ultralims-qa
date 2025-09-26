# Relatório de Testes - Desafio QA

---

## Dados do Candidato
*Dados disponíveis no documento enviado no e-mail*

- **Nome:**  
- **Email:**
- **Número Contato:**


---

## Plano de Testes

### Objetivo
Avaliar a qualidade da API, identificando falhas funcionais e de negócio através da criação de casos de testes que validem cenários positivos e negativos.

### Escopo
- **Cadastro de Usuário:** criação de novos usuários.
- **Login de Usuário:** autenticação via email/senha.
- **Calculadora de Juros:** endpoints de juros simples, compostos e simulação de parcelamento.

### Estratégia de Testes
- Testes automatizados com PHPUnit para endpoints críticos.
- Testes manuais via Postman para cenários exploratórios, casos negativos e positivos.
- Testes automatizados de segurança para identificar vulnerabilidades, incluindo validação de credenciais e prevenção de Enumeration Attacks.

### Casos de Teste 

| ID | Funcionalidade | Cenário | Entrada | Resultado Esperado | Resultado Obtido | Status |
|-|-|-|-|-|-|-|
| CT-API-001 | Endpoint Health | Saúde dos endpoints ok (Teste automatizado via PHPUnit) | `GET /health` | `200 OK`; resposta JSON contém: `"status": "ok"` | `200 OK`; resposta JSON contém: `"status": "ok"` | <span style="color:green">**Passou**</span> |
| CT-API-002 | Docs endpoint           | Verifica se o endpoint da documentação da API está ok (Teste automatizado via PHPUnit)       | `GET /`                                                                 | `200 OK`; resposta JSON contém `"title": "Challenge QA API"`; `"success": true` e contém chave `"endpoints"`                 | `200 OK`; resposta JSON contém `"title": "Challenge QA API"`; `"success": true` e contém chave `"endpoints"`                 | <span style="color:green">**Passou**</span> |
| CT-API-003 | Cálculo de juros simples | Cálculo válido (Teste automatizado via PHPUnit)             | `{"principal": 1000, "rate": 5, "time": 2}`                   | `200 OK`; resposta JSON contém: `"success": true`; `"calculation_type": "simple_interest"`; `"total_amount: 1100"`.              | `200 OK`; resposta JSON contém: `"success": true`; `"calculation_type": "simple_interest"`; `"total_amount: 1100"`.              | <span style="color:green">**Passou**</span> |
| CT-API-004 | Cálculo de juros simples | Cálculo inválido com valor de capital negativo (Teste automatizado via PHPUnit) |  `{"principal": -1000, "rate": 5, "time": 2}`                   | `400 Bad Request`; JSON retornado contém `"success": false` e chave `message` com erro genérico informando operação inválida.                                                                 | `200 OK`; Efetuando o cálculo inválido e retornando o resultado.             | <span style="color:red">**Falhou**</span> |
| CT-API-005 | Cálculo de juros simples | Falta do campo `principal` (Teste automatizado via PHPUnit)       |  `{"rate": 5, "time": 2}`                                        | `400 Bad Request`; JSON retornado contém `"success": false` e chave `message` com erro genérico informando a falta de um dos parâmetros obrigatórios.    | `400 Bad Request`; JSON retornado contém `"success": false` e chave `message` com erro genérico informando a falta de um dos parâmetros obrigatórios. | <span style="color:green">**Passou**</span> |
| CT-API-006 | Cálculo de juros simples | Falta do campo `rate` (Teste manual via Postman)            |  `{"principal": 5, "time": 2}`                                   | `400 Bad Request`; JSON retornado contém `"success": false` e chave `message` com erro genérico informando a falta de um dos parâmetros obrigatórios. | `400 Bad Request`; JSON retornado contém `{"success": false, "message": "Principal, rate, and time are required"}` | <span style="color:green">**Passou**</span> |
| CT-API-007 | Cálculo de juros simples | Falta do campo `time` (Teste manual via Postman)            |  `{"principal": 5, "rate": 2}`                                   | `400 Bad Request`; JSON retornado contém `"success": false` e chave `message` com erro genérico informando a falta de um dos parâmetros obrigatórios. | `400 Bad Request`; JSON retornado contém `{"success": false, "message": "Principal, rate, and time are required"}` | <span style="color:green">**Passou**</span> |
| CT-API-008 | Cálculo de juros compostos | Cálculo válido (Teste automatizado via PHPUnit)                          |  `{"principal": 1000, "rate": 5, "time": 2, "compounding_frequency": 12}` | `200 OK`; JSON retornado contém `"success": true`; `"total_amount": 1104.9`. | `200 OK`; JSON retornado contém `"success": true`; `"total_amount": 1104.9`. | <span style="color:green">**Passou**</span> |
| CT-API-009 | Cálculo de juros compostos | Cálculo inválido com valor de `"compounding_frequency" = 0`  (Teste automatizado via PHPUnit) |  `{"principal": 1000, "rate": 5, "time": 2, "compounding_frequency": 0}` | `400 Bad Request`; JSON contém ``"success": false`` e chave `message` com erro genérico informando operação inválida.                               | `500 Internal Server Error`; exceção `DivisionByZeroError` retornada pelo servidor, sem mensagem amigável.                                                                            | <span style="color:red">**Falhou**</span> |
| CT-API-010 | Cálculo de juros compostos | Cálculo inválido com valor de `"compounding_frequency" < 0`  (Teste automatizado via PHPUnit) |  `{"principal": 1000, "rate": 5, "time": 2, "compounding_frequency": -1}` | `400 Bad Request`; JSON contém ``"success": false`` e chave `message` com erro genérico informando operação inválida.                                                | `200 OK`; JSON retornado contém `"success": true` efetuando um cálculo inválido. | <span style="color:red">**Falhou**</span> |
| CT-API-011 | Cálculo de juros compostos | Cálculo inválido com entradas negativas (Teste automatizado via PHPUnit) |  `{"principal": -1000, "rate": -5, "time": -2, "compounding_frequency": -12}` | `400 Bad Request`; JSON contém ``"success": false`` e chave `message` com erro genérico informando operação inválida. | `200 OK`; JSON retornado contém `"success": true` e chave `"results"`, indicando que um cálculo inválido foi efetuado. | <span style="color:red">**Falhou**</span> |
| CT-API-012 | Cálculo de juros compostos | Cálculo válido sem a entrada de `"compounding_frequency"` (Teste automatizado via PHPUnit) |  `{"principal": 1000, "rate": 5, "time": 2}`                     | `200 OK`; Retornado JSON com `"compounding_frequency": 12` e `success: true`.                          | `200 OK`; Retornado JSON com `"compounding_frequency"` com o valor padrão **12** e `success: true`.                          | <span style="color:green">**Passou**</span> |
| CT-API-013 | Cálculo de juros compostos | Falta de um dos campos obrigatórios na entrada (Teste automatizado via PHPUnit) |  `{"principal": 1000, "rate": 5}`                                 | `400 Bad Request`; JSON retornado contendo `"success": false` e chave `message` com erro genérico informando a falta de um dos parâmetros obrigatórios.             | `400 Bad Request`; JSON retornado: `{"success": false, "message": "Principal, rate, and time are required"}` | <span style="color:green">**Passou**</span> |
| CT-API-014 | Cálculo de parcelamentos | Cálculo válido (Teste automatizado via PHPUnit)                          |  `{"principal": 1000, "rate": 5, "installments": 10}`           | `200 OK`; JSON retornado contém com `"success": true`; `"installment_amount": 102.3`; `"total_amount": 1023.06` e `"total_interest": 23.06` | `200 OK`; JSON retornado contém com `"success": true`; `"installment_amount": 102.3`; `"total_amount": 1023.06` e `"total_interest": 23.06` | <span style="color:green">**Passou**</span> |
| CT-API-015 | Cálculo de parcelamentos | Cálculo inválido com valor de `principal` negativo (Teste automatizado via PHPUnit) |  `{"principal": -1000, "rate": 5, "installments": 10}`          | `400 Bad Request`; JSON contém ``"success": false`` e chave `message` com erro genérico informando operação inválida.                                                | `200 OK`; JSON com `"success": true` indicando que uma operação inválida foi realizada | <span style="color:red">**Falhou**</span> |
| CT-API-016 | Cálculo de parcelamentos | Entradas recebendo tipos de dados incorretos (Teste automatizado via PHPUnit)                 |  `{"principal": "A", "rate": "A", "installments": "A"}`          | `400 Bad Request`; JSON contém ``"success": false`` e chave `message` com erro genérico informando operação inválida.                                                | `500 Internal Server Error`; exceção `DivisionByZeroError` retornada pelo servidor, sem mensagem amigável.                                                                            | <span style="color:red">**Falhou**</span> |
| CT-API-017 | Cálculo de parcelamentos | Falta de um dos campos obrigatórios na entrada (Teste automatizado via PHPUnit) |  `{"principal": 1000, "installments": 10}`                        | `400 Bad Request`; JSON retornado contendo `"success": false` e chave `message` com erro genérico informando a falta de um dos parâmetros obrigatórios.   | `400 Bad Request`; JSON retornado contendo `"success": false` e chave `message` com erro genérico informando a falta de um dos parâmetros obrigatórios.   | <span style="color:green">**Passou**</span> |
| CT-API-018 | Cálculo de parcelamentos | Cálculo inválido com valor de `installments` negativo (Teste automatizado via PHPUnit) |  `{"principal": 1000, "rate": 5, "installments": -10}`          | `400 Bad Request`; JSON contém ``"success": false`` e chave `message` com erro genérico informando operação inválida.               | `200 OK`; JSON contendo `"success": true` indicando que um cálculo inválido foi efetuado.                                                               | <span style="color:red">**Falhou**</span> |
| CT-API-019 | Cálculo de parcelamentos | Cálculo inválido com valor de `rate` negativo (Teste manual via Postman) |  `{"principal": 1000, "rate": -5, "installments": 10}`          | `400 Bad Request`; JSON contém ``"success": false`` e chave `message` com erro genérico informando operação inválida.               | `200 OK`; JSON contendo `"success": true` indicando que um cálculo inválido foi efetuado.                                                               | <span style="color:red">**Falhou**</span> |
| CT-API-020 | Cadastro de usuários    | Cadastro de usuário com entradas válidas de e-mail e senha forte (Teste automatizado via PHPUnit) |  `{"email": "email@test.com", "password": "Senh@Sup0st@men7eF0rt*"}` | `201 Created`; JSON retornado contendo `"success": true`, mensagem informando a criação do usuário e sem a chave `warning`. | `201 Created`; JSON retornado contendo `"success": true`, mensagem informando a criação do usuário, mas **com chave** `warning` inesperada. | <span style="color:red">**Falhou**</span> |
| CT-API-021 | Cadastro de usuários    | Cadastro de usuário com entradas válidas de e-mail e senha fraca (Teste automatizado via PHPUnit) |  `{"email": "email@test.com", "password": "123"}` | `201 Created`; JSON retornado contendo `"success": true`, mensagem informando a criação do usuário, com `"warning": "Password is weak but accepted"`. | `201 Created`; JSON retornado contendo `"success": true` e `"warning": "Password is weak but accepted"` | <span style="color:green">**Passou**</span> |
| CT-API-022 | Cadastro de usuários    | Cadastro de usuário com formato de e-mail inválido e senha fraca (Teste automatizado via PHPUnit) |  `{"email": "testesemarroba", "password": "123"}`               | `400 Bad Request`; JSON contém ``"success": false`` e chave `message` informando que o e-mail deve ser válido.                        | `201 Created`; JSON contendo `"success": true` | <span style="color:red">**Falhou**</span> |
| CT-API-023 | Cadastro de usuários    | Cadastro de usuário com e-mail já registrado com credenciais diferentes (Teste automatizado via PHPUnit) |  `{"email": "duplicatedemail@test.com", "password": "differentpassword"}` | `409 Conflict`; JSON retornando `"success": false` e chave `message` informando que o e-mail já está registrado. | `201 Created`; JSON contendo `"success": true` | <span style="color:red">**Falhou**</span> |
| CT-API-024 | Cadastro de usuários   | Cadastro de usuário com e-mail já registrado com credenciais iguais (Teste automatizado via PHPUnit) | `{"email": "samecredentials@test.com", "password": "anypassword"}` | `409 Conflict`; JSON retornando `"success": false` e chave `message` informando que o e-mail já está registrado. | `409 Conflict`; JSON retornando `"success": false` e ``"message": "Email already exists"``. | <span style="color:green">**Passou**</span> |
| CT-API-025 | Cadastro de usuários    | Cadastro de usuário com campo de senha vazio (Teste automatizado via PHPUnit)                 |  `{"email": "differentemail@test.com", "password": ""}`                   | `400 Bad Request`; JSON contém ``"success": false`` e chave `message` com erro informando que o campo de senha não pode ser vazio.    | `201 Created`; JSON contendo `"success": true, "message": "User registered successfully", "warning": "Password is weak but accepted"` | <span style="color:red">**Falhou**</span> |
| CT-API-026 | Cadastro de usuários    | Cadastro de usuário sem o campo de senha (Teste automatizado via PHPUnit)                 |  `{"email": "differentemail1@test.com"}`                   | `400 Bad Request`; JSON retornado contém `"success": false` e chave `message` com erro genérico informando a falta de um dos parâmetros obrigatórios.     | `400 Bad Request`; JSON retornando `"success": false` e ``"message": "Email and password are required"``. | <span style="color:green">**Passou**</span> |
| CT-API-027 | Cadastro de usuários    | Cadastro de usuário sem o campo de email (Teste automatizado via PHPUnit)                 |  `{"password": "anypassword"}`                   | `400 Bad Request`; JSON retornado contém `"success": false` e chave `message` com erro genérico informando a falta de um dos parâmetros obrigatórios.     | `400 Bad Request`; JSON retornando `"success": false` e ``"message": "Email and password are required"``. | <span style="color:green">**Passou**</span> |
| CT-API-028 | Cadastro de usuários    | Cadastro de usuário com campo de e-mail vazio (Teste automatizado via PHPUnit)                 |  `{"email": "", "password": "anypassword"}` | `400 Bad Request`; JSON contém ``"success": false`` e chave `message` informando que o e-mail deve ser válido.    | `201 Created`; JSON contendo `"success": true, "message": "User registered successfully", "warning": "Password is weak but accepted"` | <span style="color:red">**Falhou**</span> |
| CT-API-029 | Login de usuários       | Login com credenciais de usuário válidos (Teste automatizado via PHPUnit)                     |  `{"email": "validemail@test.com", "password": "mbqu2Q39NX1UB#"}`       | `200 OK`; JSON contendo `"success": true`, com chave `message` informando sucesso no login.             | `200 OK`; JSON contendo `"success": true`, com chave `message` informando sucesso no login.                           | <span style="color:green">**Passou**</span> |
| CT-API-030 | Login de usuários       | Login com usuário não cadastrado (Teste automatizado via PHPUnit) |  `{"email": "nonexistent@example.com", "password": "anypassword"}` | `401 Unauthorized`; JSON contendo `"success": false` e chave `message` informando que as credenciais são inválidas. | `404 Not Found`; JSON contendo `"success": false` e `"message": "User not found"`         | <span style="color:red">**Falhou**</span> |
| CT-API-031 | Login de usuários       | Login com senha de usuário inválida (Teste automatizado via PHPUnit)                         |  `{"email": "invalidpasswordvalidemail@test.com", "password": "mbqu232Q39NX1UB#"}`          | `401 Unauthorized`; JSON contendo `"success": false` e chave `message` genérica informando que as credenciais estão incorretas. | `401 Unauthorized`; JSON contendo `"success": false` e ``"message": "Password is incorrect"``                           | <span style="color:red">**Falhou**</span> |
| CT-API-032 | Regras de método HTTP | Endpoints POST devem rejeitar outros métodos (Teste automatizado via PHPUnit) | `GET/PUT/PATCH/DELETE` em `/api/user/register`, `/api/user/login`, `/api/calculator/*` | `405 Method Not Allowed` | `200 OK`; Indicando que pelo menos um endpoint aceitou métodos não permitidos. | <span style="color:red">**Falhou**</span> |


---

## Falhas Encontradas

1. Os testes de cálculo de juros simples, compostos e de parcelamentos com valores de entrada inválidas falharam. A API retornou status `200 OK` e realizou o cálculo, enquanto, o resultado esperado seria `400 Bad Request` com uma mensagem de erro.
  
    **Impacto:** Entradas inválidas passam sem validação, podendo gerar resultados incorretos.

2. O teste `CT-API-020` retornou `201 Created` e criou o usuário corretamente, porém, retornou `"warning": "Password is weak but accepted"` mesmo utilizando senha forte (uso de letras maiúsculas e minúsculas, números e caracteres especiais). 

    **Impacto:** Pode confundir o usuário e indica uma possível falha na regra de validação da força de senha.

3. O teste `CT-API-022` falhou ao enviar e-mail inválido e uma senha fraca. A API retornou `201 Created` com apenas um aviso de que a senha é fraca, enquanto, o esperado seria `400 Bad Request` com uma mensagem informando que o e-mail utilizado é inválido.

    **Impacto:** Permite criar contas com dados inválidos.

4. O teste `CT-API-023` falhou ao tentar registrar um usuário com e-mail já cadastrado. A API retornou `201 Created` com um aviso de senha fraca, enquanto, o esperado seria `409 Conflict` com uma mensagem de erro informando que o e-mail já está em uso. Foi observado que o **status de conflito só é retornado se o mesmo e-mail e a mesma senha forem registrados**, conforme testado no caso `CT-API-024`. 

    **Impacto:** Bug na lógica de unicidade, permitindo inconsistências no banco de dados. 

5. O teste `CT-API-025` enviou uma senha vazia. A API retornou `201 Created` e registrou o usuário, enquanto, o esperado seria `400 Bad Request` com uma mensagem de erro informando que a senha não possui os requisitos mínimos.

    **Impacto:** Regras mínimas de senha não são aplicadas.

6. O teste `CT-API-028` enviou o campo de e-mail vazio. A API retornou `201 Created` e registrou o usuário, enquanto, o esperado seria `400 Bad Request` com uma mensagem de erro informando formato de e-mail inválido.

    **Impacto:** Permite criar usuários com e-mails inválidos.

7. O teste `CT-API-030` enviou um e-mail de usuário inexistente para login. A API retornou `404 Not Found`, com uma mensagem informando que o usuário não foi encontrado, enquanto, o esperado seria `401 Unauthorized`, com uma mensagem genérica de credenciais incorretas. 

    **Impacto:** Falha de segurança, permite descobrir e-mails cadastrados.

8. O teste `CT-API-031` enviou credenciais de login incorretas para um usuário já cadastrado. A API retornou `401 Unauthorized`, com uma mensagem informando a senha está incorreta, enquanto, o esperado seria `401 Unauthorized` com uma mensagem genérica de credenciais incorretas. 

    **Impacto:** Facilita ataques de enumeração de contas.

9. O teste `CT-API-032` verificou que endpoints que deveriam aceitar apenas o método `POST` permitiram outros métodos (`GET`, `PUT`, `PATCH`, `DELETE`), retornando `200 OK` em vez de `405 Method Not Allowed`.

    **Impacto:** Possibilidade de comportamentos inesperados, aumento de superfície de ataque e dificuldade de observabilidade.

---

## Sugestões de Melhoria

1. Adicionar validação para todos os parâmetros de entrada nos endpoints de cálculos, retornando `400 Bad Request` com mensagens de erros genéricas para operações inválidas.

2. Definir os requisitos mínimos para a criação de senhas de usuário, exigindo comprimento mínimo, letras maiúsculas e minúsculas, números e caracteres especiais e documentá-los na especificação da API.

3. Implementar validação que rejeite a criação de usuários com senhas fracas que não atendam aos requisitos mínimos, retornando status HTTP 400 com mensagem clara de erro.

4. Ajustar a lógica de classificação de senhas para que apenas senhas que cumpram os critérios mínimos sejam consideradas fortes, evitando falsos negativos na validação.

5. Validar o formato de e-mail no cadastro de usuário, aceitando apenas endereços válidos, garantindo que funcionalidades futuras baseadas no e-mail (reset de senha, notificações) funcionem corretamente. 

6. Prevenir o cadastro de e-mails duplicados, retornando `409 Conflict` caso o endereço já esteja registrado.

7. Ajustar a lógica de usuários duplicados, validar somente o e-mail já registrado sem considerar a senha do usuário.

8. Padronizar mensagens de erro de login com credenciais inválidas em todos os casos de falha, evitando exposição de informações sensíveis sobre existência de usuários.

9. Padronizar todas as respostas de erros com um identificador fixo. Atualmente, a API não possui padrão definido para todos os erros. Isso pode causar **ambiguidade**, pois o mesmo código de status pode significar erros diferentes. 

    **Sugestão de padronização das respostas de erro:**

    `"success": boolean` → sempre `false` em erros;   
    `"message": string` → texto legível para humanos;  
    `"error_code": string` → identificador técnico fixo.

    **Exemplo de payload de erro (e-mail inválido):**
    ```json
    {
      "success": false,
      "message": "The e-mail format provided is invalid.",
      "error_code": "INVALID_EMAIL_FORMAT"
    }
    ```    

10. Armazenar senhas de forma segura no banco de dados, nunca em texto puro (plain text). Utilizar funções de hash específicas para senhas.

---

## Desenvolvimento / Extras

Se o candidato criou **testes automatizados**, scripts ou qualquer ferramenta extra, pode documentar aqui:  
- Ferramentas utilizadas: 
  - **Postman** para testes manuais; 
  - **PHPUnit** para testes automatizados;
  - **Doctrine** e **MySQL** para manipulação e verificação do banco de dados. 
- Repositório ou anexo: 
  - **[Repositório:](https://github.com/carlosdamaia/ultralims-qa)** *Preferencial*, principal fonte para acesso ao código.
  - **[Anexo:](https://1drv.ms/f/c/a8e90bfcca3a1702/Ek91k3ZHv5lCh4vHnaibImMBLX6f1C5eT7QG5kxlT6bl9g)** *Opcional*, se o repositório estiver indisponível.
