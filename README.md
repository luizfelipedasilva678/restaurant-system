# Restaurant System

This project was part of an college assignment where the main goal was to develop
a system for a restaurant where the user can create reservations and manage the tables
being attended.

## Project structure

### /backend

Folder that contains code responsible for the backend.

### /frontend

Folder that contains code responsible for the frontend.

### /docs

Folder with some system diagrams.

### /.vscode

Folder with some vscode configs and some extensions recommendations.

## How to run the project locally

### Database

To start the mariadb just run `docker compose up -d` and the test and app database will
be created with sqls scripts pre-loaded. If you encounter any error the sql
scripts can be found in `/backend/db`.

Together with mariadb, `Adminer` a database manager, will also start
by default it will start on port 8080.

### Backend

First of all its necessary to write an .env. Create a .env file in /backend/config and
paste the content below and fill with your information:

```
HOST_URL=
ENV=
DATABASE_USER=
DATABASE_PASSWORD=
DATABASE_NAME=
DATABASE_HOST=
PASSWORD_PEPPER_PREFIX=
PASSWORD_PEPPER_SUFFIX=
```

To start the backend run:

- `cd backend`
- `composer install`
- `composer run-script dev`

To format the code run `composer run-script format`.

To execute phpstan run `composer run-script lint`.

Finally, to run the tests execute `composer run-script test`.

### Frontend

To start the frontend run:

- `cd frontend`
- `pnpm i`
- `pnpm run dev`

To format the code run `pnpm run format`.

To execute the linter run `pnpm run lint`.

To execute the unit tests run `pnpm run test`.

To execute the e2e tests run `pnpm run e2e` (obs: Para rodar o teste e2e é necessário estar com o frontend rodando).

## ER Diagram

![er](/docs/er-diagram.png)

## Essential model

![er](/docs/essential-model.png)

## Partial model for reservation creation

![er](/docs/partial-diagram-reservation-creation.png)

## Partial model to add consumption to a table

![er](/docs/partial-diagram-consumptions.png)

## API

**POST** - /api/v1/orders/fulfill

Complete an order.

expected body

```
{
  "employeeId": number,
  "paymentMethodId": number,
  "orderId": number,
  "total": number,
  "discount": number
}
```

**POST** - /api/v1/orders/{id}/items

Add items to a given order.

expected order

```
{
  items: [
    {
      itemId: number,
      quantity: number
    }
  ]
}
```

**GET** - /api/v1/orders/{id}

Returns an order

**GET** - /api/v1/orders

Returns all incomplete orders

**POST** - /api/v1/orders

Create an order

expected order

```
{
  "clientName": string,
  "tableId": number,
}
```

**GET** - /api/v1/reports/sales-by-category

Returns the sales by category

Params:

- initialDate

  - Required.
  - Initial date for the period.

- finalDate
  - Required.
  - Final date for the period.

**GET** - /api/v1/reports/sales-by-day

Returns the sales by day

Params:

- initialDate

  - Required.
  - Initial date for the period.

- finalDate
  - Required.
  - Final date for the period.

**GET** - /api/v1/reports/sales-by-employee

Return the sales by employee

Params:

- initialDate

  - Required.
  - Initial date for the period.

- finalDate
  - Required.
  - Final date for the period.

**GET** - /api/v1/reports/sales-by-payment-method

Returns the sales by payment method

Params:

- initialDate

  - Required.
  - Initial date for the period.

- finalDate
  - Required.
  - Final date for the period.

**GET** - /api/v1/payments-methods

Returns all the available payment methods

**GET** - /api/v1/items

Returns all items

Params:

- page

  - Optional.
  - Determine the page.
  - Must to be a number.

- perPage

  - Optional.
  - Determine the items per page.
  - Must to be a number.

**POST** - /api/v1/auth/login

Make login in application

expected body

```
{
  "login": string,
  "password": string,
}
```

**GET** - /api/v1/auth/logout

Make logout

**GET** - /api/v1/auth/session

Get data from the current session

**GET** - /api/v1/employees

Returns the employees.

Params:

- page

  - Optional.
  - Determine the page.
  - Must to be a number.

- perPage

  - Optional.
  - Determine the items per page.
  - Must to be a number.

**GET** - /api/v1/tables

Returns the tables.

Params:

- startDate
  - Optional.
  - Returns the occupied tables for the given date.
  - Must be a valid date and cannot be in the past.

**GET** - /api/v1/reservations

Returns the reservation.

Params:

- currentAndLater

  - Optional.
  - Determine if only reservations for the current day will be returned.
  - Must be 'true' or 'false'

- page

  - Optional.
  - Determine the page.
  - Must to be a number.

- perPage

  - Optional.
  - Determine the items per page.
  - Must to be a number.

- initialDate

  - Required.
  - Initial date for the period.

- finalDate
  - Required.
  - Final date for the period.

**GET** - /api/v1/reservations/{id}

Returns a given reservation

**POST** - /api/v1/reservations

Create a reservation.

expected body

```

{
"clientName": string,
"employeeId": number,
"startTime": string (ex: "2024-12-03 03:23:00"),
"tableId": number
}

```

**PATCH** - /api/v1/reservations/{id}

Updates a reservation.

expected body

```

{
"status": "inactive"
}

```

Currently only the body above is accepted.

## Authors

- [Luiz Felipe](https://github.com/luizfelipedasilva678)
- [Pedro Henrique](https://github.com/PedroHenrique-git)

## References

- SVGREPO. [https://www.svgrepo.com/svg/164649/restaurant-table-and-chairs](https://www.svgrepo.com/svg/164649/restaurant-table-and-chairs)

- BiomeJS Documentation. [https://biomejs.dev/guides/getting-started/](https://biomejs.dev/guides/getting-started/)

- PHPStan Documentation. [https://phpstan.org/user-guide/getting-started](https://phpstan.org/user-guide/getting-started)

- Char.js Documentation. [https://www.chartjs.org/](https://www.chartjs.org/)

- PHP Coding Standards Fixer Documentation. [https://cs.symfony.com/](https://cs.symfony.com/)

- Kahlan Documentation. [https://kahlan.github.io/docs/](https://kahlan.github.io/docs/)

- Vitest Documentation. [https://vitest.dev/](https://vitest.dev/)

- Vite Documentation. [https://v5.vite.dev/](https://v5.vite.dev/)

- Playwright Documentation. [https://playwright.dev/](https://playwright.dev/)

- Bulma Documentation. [https://bulma.io/](https://bulma.io/)

- Path to regex Documentation. [https://github.com/pillarjs/path-to-regexp](https://github.com/pillarjs/path-to-regexp)

- PHP Documentation. [https://www.php.net/docs.php](https://www.php.net/docs.php)
