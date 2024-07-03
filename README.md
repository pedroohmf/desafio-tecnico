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

```markdown
POST /deposito/{idConta}/{moeda}/{valor} - Cria um novo depósito
```

# Saldo

```markdown
POST /saldo/{idConta}/{moeda?} - Consulta o saldo
```

# Saque

```markdown
POST /saldo/{idConta}/{moeda?} - Efetua um saque
```
