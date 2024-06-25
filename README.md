# API RESTful

Este projeto é uma API criada usando Laravel 11.

1. Clone o repositório

```bash
git clone https://github.com/pedroohmf/desafio-tecnico
```

## Como Usar

1. Execute o seguinte comando para instalar as dependências do Laravel listadas no arquivo composer.json:

```markdown
composer install
```

2. Renomeie o .env.example para .env

```markdown
Acesse o diretório raiz do seu projeto Laravel, lá encontrará um arquivo com o nome .env.example, basta renomear para .env
```

3. Crie uma nova chave para a aplicação. Para realizar o procedimento, utilize o seguinte comando:

```markdown
php artisan key:generate
```

4. Configurando o arquivo .env
Configure de acordo com seu banco de dados e servidor e depois rodar as migrations. E após configurar o .env, para rodar as migrations utilize o seguinte comando:

```markdown
php artisan migrate
```

5. Inicie o servidor de desenvolvimento:

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

# Saldo (Em progresso)

```markdown
POST /saldo/{idConta}/{moeda?} - Consulta o saldo
```

# Saque (Em breve)

```markdown
POST /saldo/{idConta}/{moeda?} - Efetua um saque
```
