# About the API

This is a mini API for managing users & groups.

## 1. Clone the repository
`git clone git@github.com:abdeljabar/usergroups.git`

## 2. CD to the new directory
`cd usergroups`

## 3. Build the containers
`docker-compose up --build`

## 4. Run composer install
`docker-compose run php composer install`

## 5. Create database
`docker-compose run php bin/console d:d:c`

## 6. Run doctrine migrations
`docker-compose run php bin/console d:m:m`

## 7. Load doctrine fixtures
`docker-compose run php bin/console d:f:l`

## 8. Link to the app
`http://localhost:8080`

# Automatic tests

## 1. Create database
`docker-compose run php bin/console d:d:c --env=test`

## 2. Create database
`docker-compose run php bin/console d:m:m --env=test`

## 3. Create database
`docker-compose run php bin/phpunit`

# Getting started with the Api

## Get paginated users
`GET http://localhost:8080/users`

## Get user details
`GET http://localhost:8080/users/{id}`

## Get paginated groups
`GET http://localhost:8080/groups`

## Get group details
`GET http://localhost:8080/groups/{id}`

## Admin authentication

To login please send json in the body of this uri. A fresh token will be generated for you to use in the endpoints that need authentication.
`POST http://localhost:8080/authentication_token`

The json body:

`{
"username": "admin",
"password": "password"
}`

Response body:

{
"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NDc3MjkxMzAsImV4cCI6MTY0NzczMjczMCwicm9sZXMiOlsiUk9MRV9BRE1JTiJdLCJ1c2VybmFtZSI6ImFkbWluIn0.f85h6fPgy7Zj5bjMqa0AwIWLql1YiX0cBsMGd0adFhxVkaQDH3i1B0ZIbi8RulTVSZvPIdaKGThbu5LjdsqMROjR4zU0P8FCF3J6l0MDNGdXEKlPQiC1MBTAo3WJ8-tGP6kSmCiJpmq5u68H4bA9ld9UTm63YO1Fx6HgkakSuv6a00aRUQIQ-JEO6cmhM6Xn6Tb0Qvg7TL-30r1_njhMkOeHaYScts8J-jGQOnXXvzYascBDDSFQV2XqTiwv2asHW9tLGuq3QWFxlTHA33P1nSGDaSvq4xUHaalM2Q_ozkZ2bYStjpbrcqQnQd27zdpZ6rQVfQNhFNNPef9yh62HmQ"
}

Add the new generated token to the headers of the rest of the endpoints like this:

`-H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NDc3MjkxMzAsImV4cCI6MTY0NzczMjczMCwicm9sZXMiOlsiUk9MRV9BRE1JTiJdLCJ1c2VybmFtZSI6ImFkbWluIn0.f85h6fPgy7Zj5bjMqa0AwIWLql1YiX0cBsMGd0adFhxVkaQDH3i1B0ZIbi8RulTVSZvPIdaKGThbu5LjdsqMROjR4zU0P8FCF3J6l0MDNGdXEKlPQiC1MBTAo3WJ8-tGP6kSmCiJpmq5u68H4bA9ld9UTm63YO1Fx6HgkakSuv6a00aRUQIQ-JEO6cmhM6Xn6Tb0Qvg7TL-30r1_njhMkOeHaYScts8J-jGQOnXXvzYascBDDSFQV2XqTiwv2asHW9tLGuq3QWFxlTHA33P1nSGDaSvq4xUHaalM2Q_ozkZ2bYStjpbrcqQnQd27zdpZ6rQVfQNhFNNPef9yh62HmQ'`

## To create a new user
`POST http://localhost:8080/users`

The json body:

`{
"firstName": "John",
"lastName": "Doe",
"email": "taoufikallah@gmail.com",
"phone": "066900000",
"age": 27,
"type": "Test"
}`

## To update a user
`PUT http://localhost:8080/users/{id}`

The json body:

`{
"firstName": "John Edited"
}`

## To attach a group to user
`PUT http://localhost:8080/user_groups/{id}`

The json body:

`{
"group": "/groups/1"
}`

## To delete a user
`DELETE http://localhost:8080/users/{id}`

## To create a new group
`POST http://localhost:8080/groups`

The json body:

`{
"name": "The Old Generation",
"description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea "
}`

## To update a group
`PUT http://localhost:8080/groups/{id}`

The json body:

`{
"name": "The New Generation"
}`

## To delete a group
`DELETE http://localhost:8080/groups/{id}`