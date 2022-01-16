Prerequisities:

symfony 5.2.14 PHP 7.3.33
edit .env file in root and set proper mysql credentials. create database named crud mentioned there in mysql

Then do 

composer update
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
symfony serve

User with username: kareem@gmail.com / password: kareem ROLE_ADMIN ll be created

Auth of api routes done using jwt token. private key / public key available inside config/jwt/

$ openssl genpkey -out config/jwt/private.pem-back -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
$ openssl pkey -in config/jwt/private.pem-back -out config/jwt/public.pem -pubout
passphrase 123456

The same used in .env file

Here are routes for soccer CRUD API &#8595;

api_login_check:
  path: /api/login_check
  methods: [ POST ]

![](api-screenshots/api-login_check.png)

curl -X POST \
  http://127.0.0.1:8000/api/login_check \
  -H 'cache-control: no-cache' \
  -H 'content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW' \
  -H 'postman-token: e9048ca9-da83-3937-1fba-f3df89d26496' \
  -F password=kareem \
  -F username=kareem@gmail.com

list_teams:
  path: /list/teams
  controller: App\Controller\TeamController::list
  methods: [ GET ]

curl -X GET \
  http://127.0.0.1:8000/list/teams 

![](api-screenshots/listTeams.png)

list_team_players:
  path: /list/players/{teamId}
  controller: App\Controller\TeamController::listPlayers
  methods: [ GET ]

curl -X GET   http://127.0.0.1:8000/list/players/1

![](api-screenshots/listTeamPlayers.png)

offset and limit can be used to paginate the players default offset is 0 and limit is 10

http://127.0.0.1:8000/list/players/1?offset=0&limit=1

create_team:
  path: /api/team/create
  controller: App\Controller\TeamController::create
  methods: [POST]

curl -X POST \
  http://127.0.0.1:8000/api/team/create \
  -H 'authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NDIzMDcxOTQsImV4cCI6MTY0MjMxMDc5NCwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImthcmVlbUBnbWFpbC5jb20ifQ.RWr8ycWJBI9MswacGrAqCFcLIQIbiGqTPaMY-OBrPXruU7xY3fPIDNF8VZEBYeon2hhX-I8g8Q6sJU8VFU46U9RDhQiWC_HkzwxhRvl8yf39oparngY6op2ZHhVHbJVPIgsJ0XhsG2-cM1c9hchRnCYypWth2Q4b2YEdBlje34UxQzYbmIVMRjRe-aJumPn13pKW35O-VS0DcAJpa33upU8eaNCBCetOebV_STqoNzIpIn_ep-AQCuJVS_Dz5ROcHejg9MCWHO7hVGqY4d3Yiw6fsNt9VyyQ25bDfzzH3JyZ1AN2rlnEGn0km0iX4L3XZoXw9rt1Ya59cdqvAqajdGcZrmA0_CTJY9guwh9GYH7CHWonbUspwPz2kTooyWpMFHHy7h4d8GLfy574Gh-k755yc5QkMfXyU0ri8Dw0XvqVISwAbKis0Uh_opvNqvhvxqkSxGnTwqLKOOXWvGk8vpDKaqs5TtKp572ktnzeTboqOIJR3j8nMqGIUcKOAuVjNf6EYAVaF6N1-XIYAJU5xLbGq1MrNniH00iIgQFKRipFsd5_uEB_fIot72mgCMqG-eJrGtzz_Y6JdWOnG8SxqDiOYaXk_si_BLnIVxqvq3NBqkCZNHvRat0nr2m1NlKbem5_fsx-b5buIpe1-SETjDvyoFMat-Lk-92E89yHoas' \
  -H 'cache-control: no-cache' \
  -H 'content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW' \
  -H 'postman-token: 130aba8a-706e-7920-6c3b-6dabbf9a9906' \
  -F teamName=abcdtest \
  -F teamLogo=https://logo.clearbit.com/ajax.nl

![](api-screenshots/createTeam.png)

teamName shouldnt be empty and teamLogo should be URL validated

edit_team:
  path: /api/team/edit
  controller: App\Controller\TeamController::edit
  methods: [ PUT ]

curl -X POST \
  http://127.0.0.1:8000/api/team/edit \
  -H 'authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NDIzMDcxOTQsImV4cCI6MTY0MjMxMDc5NCwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImthcmVlbUBnbWFpbC5jb20ifQ.RWr8ycWJBI9MswacGrAqCFcLIQIbiGqTPaMY-OBrPXruU7xY3fPIDNF8VZEBYeon2hhX-I8g8Q6sJU8VFU46U9RDhQiWC_HkzwxhRvl8yf39oparngY6op2ZHhVHbJVPIgsJ0XhsG2-cM1c9hchRnCYypWth2Q4b2YEdBlje34UxQzYbmIVMRjRe-aJumPn13pKW35O-VS0DcAJpa33upU8eaNCBCetOebV_STqoNzIpIn_ep-AQCuJVS_Dz5ROcHejg9MCWHO7hVGqY4d3Yiw6fsNt9VyyQ25bDfzzH3JyZ1AN2rlnEGn0km0iX4L3XZoXw9rt1Ya59cdqvAqajdGcZrmA0_CTJY9guwh9GYH7CHWonbUspwPz2kTooyWpMFHHy7h4d8GLfy574Gh-k755yc5QkMfXyU0ri8Dw0XvqVISwAbKis0Uh_opvNqvhvxqkSxGnTwqLKOOXWvGk8vpDKaqs5TtKp572ktnzeTboqOIJR3j8nMqGIUcKOAuVjNf6EYAVaF6N1-XIYAJU5xLbGq1MrNniH00iIgQFKRipFsd5_uEB_fIot72mgCMqG-eJrGtzz_Y6JdWOnG8SxqDiOYaXk_si_BLnIVxqvq3NBqkCZNHvRat0nr2m1NlKbem5_fsx-b5buIpe1-SETjDvyoFMat-Lk-92E89yHoas' \
  -H 'cache-control: no-cache' \
  -H 'content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW' \
  -H 'postman-token: ae7b7644-6fd8-4047-31c6-dd81f39cdc24' \
  -F teamName=abcdtest \
  -F teamLogo=https://logo.clearbit.com/ajax.nl \
  -F _method=PUT \
  -F teamId=5

teamName shouldnt be empty and teamLogo should be URL validated, teamId should be present to be edited	

![](api-screenshots/editTeam.png)


delete_team:
  path: /api/team/delete
  controller: App\Controller\TeamController::delete
  methods: [ DELETE ]

curl -X POST \
  http://127.0.0.1:8000/api/team/delete \
  -H 'authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NDIzMDcxOTQsImV4cCI6MTY0MjMxMDc5NCwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImthcmVlbUBnbWFpbC5jb20ifQ.RWr8ycWJBI9MswacGrAqCFcLIQIbiGqTPaMY-OBrPXruU7xY3fPIDNF8VZEBYeon2hhX-I8g8Q6sJU8VFU46U9RDhQiWC_HkzwxhRvl8yf39oparngY6op2ZHhVHbJVPIgsJ0XhsG2-cM1c9hchRnCYypWth2Q4b2YEdBlje34UxQzYbmIVMRjRe-aJumPn13pKW35O-VS0DcAJpa33upU8eaNCBCetOebV_STqoNzIpIn_ep-AQCuJVS_Dz5ROcHejg9MCWHO7hVGqY4d3Yiw6fsNt9VyyQ25bDfzzH3JyZ1AN2rlnEGn0km0iX4L3XZoXw9rt1Ya59cdqvAqajdGcZrmA0_CTJY9guwh9GYH7CHWonbUspwPz2kTooyWpMFHHy7h4d8GLfy574Gh-k755yc5QkMfXyU0ri8Dw0XvqVISwAbKis0Uh_opvNqvhvxqkSxGnTwqLKOOXWvGk8vpDKaqs5TtKp572ktnzeTboqOIJR3j8nMqGIUcKOAuVjNf6EYAVaF6N1-XIYAJU5xLbGq1MrNniH00iIgQFKRipFsd5_uEB_fIot72mgCMqG-eJrGtzz_Y6JdWOnG8SxqDiOYaXk_si_BLnIVxqvq3NBqkCZNHvRat0nr2m1NlKbem5_fsx-b5buIpe1-SETjDvyoFMat-Lk-92E89yHoas' \
  -H 'cache-control: no-cache' \
  -H 'content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW' \
  -H 'postman-token: 9088dddf-f94e-f15d-5ae1-d0d89ff7aae0' \
  -F _method=DELETE \
  -F teamId=5

teamId should be present to be deleted. If players are associated with team then delete fails.

![](api-screenshots/deleteTeam.png)

create_player:
  path: /api/player/create
  controller: App\Controller\PlayerController::create
  methods: [POST]

curl -X POST \
  http://127.0.0.1:8000/api/player/create \
  -H 'authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NDIyMjA5MjIsImV4cCI6MTY0MjIyNDUyMiwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImthcmVlbUBnbWFpbC5jb20ifQ.HhsFrfmzG3nX7gTNn3zLTHX6cSkui2QxQkBjEL4aeiJo7gUfH0gqPip_yOAGEwpSXl6ZySokWCtF0mvw0QSynq5-Aq_beuOcf3sRJSOdbsnMNR-FNaeiD4OFgkk6xTkCe1tH49bmKqyuOMyGPRh8zxAWNZ3lS0tQupPQlESW2YtYpB0Uc-_3k5GX6XpSHph3VbS9x3QKBAcexKVLeGoTXxRNneukw-WAHmgk-pGcHIjujUUwYS8Si-WX6Pb7jk54CPp3fuqIh_KKNvdTvUice6FjnagJQje7a5gznNYlBpR6B7mUhbzRn5J8es8N4Q7VcILDC3eSxxf3FUVtT5bTOQ' \
  -H 'cache-control: no-cache' \
  -H 'content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW' \
  -H 'postman-token: c06ee5c7-33ae-d2a5-80f9-d6dea1e166e3' \
  -F 'firstName=first name' \
  -F teamId=3 \
  -F lastName=lastname \
  -F imageURI=https://pbs.twimg.com/profile_images/1038404156493635584/vd5M4ah5.jpg

firstName, lastName, imageURI, teamId the player belongs to are pre requisites

![](api-screenshots/createPlayer.png)

edit_player:
  path: /api/player/edit
  controller: App\Controller\PlayerController::edit
  methods: [ PUT ]

curl -X POST \
  http://127.0.0.1:8000/api/player/edit \
  -H 'authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NDIzMDcxOTQsImV4cCI6MTY0MjMxMDc5NCwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImthcmVlbUBnbWFpbC5jb20ifQ.RWr8ycWJBI9MswacGrAqCFcLIQIbiGqTPaMY-OBrPXruU7xY3fPIDNF8VZEBYeon2hhX-I8g8Q6sJU8VFU46U9RDhQiWC_HkzwxhRvl8yf39oparngY6op2ZHhVHbJVPIgsJ0XhsG2-cM1c9hchRnCYypWth2Q4b2YEdBlje34UxQzYbmIVMRjRe-aJumPn13pKW35O-VS0DcAJpa33upU8eaNCBCetOebV_STqoNzIpIn_ep-AQCuJVS_Dz5ROcHejg9MCWHO7hVGqY4d3Yiw6fsNt9VyyQ25bDfzzH3JyZ1AN2rlnEGn0km0iX4L3XZoXw9rt1Ya59cdqvAqajdGcZrmA0_CTJY9guwh9GYH7CHWonbUspwPz2kTooyWpMFHHy7h4d8GLfy574Gh-k755yc5QkMfXyU0ri8Dw0XvqVISwAbKis0Uh_opvNqvhvxqkSxGnTwqLKOOXWvGk8vpDKaqs5TtKp572ktnzeTboqOIJR3j8nMqGIUcKOAuVjNf6EYAVaF6N1-XIYAJU5xLbGq1MrNniH00iIgQFKRipFsd5_uEB_fIot72mgCMqG-eJrGtzz_Y6JdWOnG8SxqDiOYaXk_si_BLnIVxqvq3NBqkCZNHvRat0nr2m1NlKbem5_fsx-b5buIpe1-SETjDvyoFMat-Lk-92E89yHoas' \
  -H 'cache-control: no-cache' \
  -H 'content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW' \
  -H 'postman-token: 422d56dd-f2a5-23a1-fafc-cab0a067e332' \
  -F 'firstName=first name' \
  -F teamId=3 \
  -F lastName=lastname \
  -F imageURI=https://pbs.twimg.com/profile_images/1038404156493635584/vd5M4ah5.jpg \
  -F playerId=4 \
  -F _method=PUT


firstName, lastName, imageURI, teamId the player belongs to are pre requisites
playerId to be edited is to be mentioned. if teamid not present edit fails


![](api-screenshots/editPlayer.png)

delete_player:
  path: /api/player/delete
  controller: App\Controller\PlayerController::delete
  methods: [ DELETE ]


curl -X POST \
  http://127.0.0.1:8000/api/player/delete \
  -H 'authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NDIzMDcxOTQsImV4cCI6MTY0MjMxMDc5NCwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImthcmVlbUBnbWFpbC5jb20ifQ.RWr8ycWJBI9MswacGrAqCFcLIQIbiGqTPaMY-OBrPXruU7xY3fPIDNF8VZEBYeon2hhX-I8g8Q6sJU8VFU46U9RDhQiWC_HkzwxhRvl8yf39oparngY6op2ZHhVHbJVPIgsJ0XhsG2-cM1c9hchRnCYypWth2Q4b2YEdBlje34UxQzYbmIVMRjRe-aJumPn13pKW35O-VS0DcAJpa33upU8eaNCBCetOebV_STqoNzIpIn_ep-AQCuJVS_Dz5ROcHejg9MCWHO7hVGqY4d3Yiw6fsNt9VyyQ25bDfzzH3JyZ1AN2rlnEGn0km0iX4L3XZoXw9rt1Ya59cdqvAqajdGcZrmA0_CTJY9guwh9GYH7CHWonbUspwPz2kTooyWpMFHHy7h4d8GLfy574Gh-k755yc5QkMfXyU0ri8Dw0XvqVISwAbKis0Uh_opvNqvhvxqkSxGnTwqLKOOXWvGk8vpDKaqs5TtKp572ktnzeTboqOIJR3j8nMqGIUcKOAuVjNf6EYAVaF6N1-XIYAJU5xLbGq1MrNniH00iIgQFKRipFsd5_uEB_fIot72mgCMqG-eJrGtzz_Y6JdWOnG8SxqDiOYaXk_si_BLnIVxqvq3NBqkCZNHvRat0nr2m1NlKbem5_fsx-b5buIpe1-SETjDvyoFMat-Lk-92E89yHoas' \
  -H 'cache-control: no-cache' \
  -H 'content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW' \
  -H 'postman-token: 6182a5a8-9977-ce87-84ff-c7d532af5ada' \
  -F playerId=4 \
  -F _method=DELETE

playerId to be deleted

![](api-screenshots/deletePlayer.png)


Basic unit test for api check is available to check valid login, team create, anonymous access to team list, non anonymous access to create team fails.

do symfony serve from root and do php vendor/bin/phpunit to run api test cases

Just in case new users are required can register the same using register route




