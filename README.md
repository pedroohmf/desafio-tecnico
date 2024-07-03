# API RESTful

Este projeto é uma API criada usando Laravel 11.

1. Clone o repositório

```bash
git clone https://github.com/pedroohmf/desafio-tecnico
```

## Como Usar

2. Certifique-se de ter o Composer instalado

Após clonar e acessar o diretório do projeto, você precisa instalar as dependências Laravel do mesmo, e para realizar o procedimento é necessário ter o composer instalado.
Verifique se o Composer está instalado em seu sistema. Se você não tiver o Composer instalado, acesse o site oficial do Composer (<https://getcomposer.org/>) e siga as instruções de instalação adequadas para o seu sistema operacional.

3. Instalar as dependências do Laravel com composer

Execute o seguinte comando para instalar as dependências do Laravel listadas no arquivo composer.json:

```markdown
composer install
```

4. Renomeie o .env.example para .env

```markdown
Acesse o diretório raiz do seu projeto Laravel, lá encontrará um arquivo com o nome .env.example, basta renomear para .env
```

5. Crie uma nova chave para a aplicação. Para realizar o procedimento, utilize o seguinte comando

```markdown
php artisan key:generate
```

6. Configurando o arquivo .env de acordo com seu banco de dados e servidor, depois rode as migrations. Após configurar o .env, para rodar as migrations utilize o seguinte comando

```markdown
php artisan migrate
```

7. Inicie o servidor de desenvolvimento

```markdown
php artisan serve
```

## API Endpoints

```markdown
POST /criarconta - Cria uma nova conta e retorna o id cara conta para realizar as transações
```

# Depósito

Descrição: Realiza um depósito na conta.

```markdown
POST /deposito - Cria um novo depósito
```

Parâmetros no Body (JSON):

idConta (string) Obrigatório: O ID da conta.
valor (number) Obrigatório: O valor a ser depositado.
moeda (string) Obrigatório: A sigla da moeda do depósito.

# Saldo

Descrição: Obtém o saldo da conta. Pode ser solicitado o saldo total ou o saldo em uma moeda específica.

```markdown
POST /saldo - Consulta o saldo
```

Parâmetros no Body (JSON):

idConta (string) Obrigatório: O ID da conta.
moeda (string) Opcional: A sigla da moeda para obter o saldo específico.

# Saque

Descrição: Realiza um saque na conta.

```markdown
POST /saldo - Efetua um saque
```

Parâmetros no Body (JSON):

idConta (string) Obrigatório: O ID da conta.
valor (number) Obrigatório: O valor a ser sacado.
moeda (string) Obrigatório: A sigla da moeda do saque.
