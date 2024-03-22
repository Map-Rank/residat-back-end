## Application Deployment

1. Clone the project.

2. Copy .env.example to .env:

3. Generate an application key:

## Configuring the .env File

1. Configure the following variables in the .env file:
- DB_DATABASE=
- DB_USERNAME=root (default)
- DB_PASSWORD=
- APP_URL=http://localhost:8000

---- Add these variables to the end of the .env file ----
- WWWUSER=1000
- WWWGROUP=1000
- PWD=chemin_au_projet

## Docker Setup

1. Install Docker.

2. Restart your computer.

## Command Line Interface

- Navigate to the project directory.
- Run the command:

## Invite de commande 
- Se placer dans le repertoire du projet
- saisir la commande 
" docker-compose up -d "

## Generating Documentation
Run the following command to generate documentation:
- php artisan scribe:generate

## Accessing the Documentation
You can access the documentation at:

http://localhost:8000/docs

## Deployment of Test Coverage and Running Tests

### Test Coverage Deployment

1. Ensure that you have PHPUnit installed in your Laravel project.

2. To generate a code coverage report, run the following command:

-php artisan test --coverage-html=coverage

or 

-php artisan test --coverage-html reports/


3. Once the command is executed successfully, you can find the HTML coverage report in the `coverage` directory.

### Running Tests

To run tests in your Laravel project, use the following command:

php artisan test

This command will execute all the test cases defined in your project and display the results.

## Additional Notes

- You can customize your PHPUnit configuration by editing the `phpunit.xml` file in the root directory of your Laravel project.

- Ensure that your tests are comprehensive and cover all critical functionalities of your application.

- Regularly run tests to maintain code quality and identify any regressions introduced during development.

By following these steps, you can effectively deploy test coverage reports and run tests in your Laravel project, ensuring the reliability and stability of your application.
