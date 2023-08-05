#parspack-task
<p align="center ">
</p>

## About This App

This app manages app subscription status by platform
</br>
It is a task from parspack.com

Its features include the following :

- Seeders & Factories
- Jobs && Schedules
- Use Basic Auth For Admin Request
- Users,Platforms,Apps,Subscriptions,...
- Notification && SendMail
- Mocking Services For GooglePlay And AppStore
- Safe Style Codes
- Delete Waiting Lists
- As Much As Possible Solid & Clean Code
- RestFulApi
- Make Feature Tests For All Of Apis & Command
- ...

## Installation
```
# install dependencies
composer install

# create .env file and generate the application key

cp .env.example .env
php artisan key:generate

# make database and run seeder

php artisan migrate --seed

# you can user postman collection in doc folder

# Then launch the server:

php artisan serve
```

## Security Vulnerabilities

If you discover a security vulnerability within this app, please send an e-mail to Farid Shishebori via [faridnewepc78@gmail.com](mailto:faridnewepc78@gmail.com). All security vulnerabilities will be promptly addressed.

## License

This application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
