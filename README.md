# Guessing Game

The program picks a random number from 1 to 10. The user gets three guesses. As soon as the user enters the correct number the program writes a winning message and exits. If the user fails to enter the correct number in three guesses, the program writes a failure message and exits. The program also writes "cold" when the guess is 3 or more away from the correct answer, "warm" when the guess is 2 away, and "hot" when the guess is 1 away.


Implemented guessing game functionality in 3 ways using Zend Framework 2

#### 1. Using Sessions

URL - http://localhost:8080/index/index

#### 2. Using Cookies

URL -  http://localhost:8080/index/cookie

#### 3. Using Hidden values

URL -  http://localhost:8080/index/hidden


## Steps to run the application:

1. Configured below script in composer.json

    `"scripts": {
           "run-game": "cd public && php -S localhost:8080 index.php"
    }`
2. To run the project use `composer run-game` command in command prompt
