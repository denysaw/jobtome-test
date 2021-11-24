# Jobtome

## Task description
Create and document a small web service exposing URL shortening functions.
One should be able to create, read, and delete shortened URLs.
The API functions will be exposed under the '/api' path while accessing a shortened URL at the root level will cause redirection to the shortened URL.

## Rules of the game
- Code in PHP, ideally using the Symfony framework
- It's ok to forget about permissions (everyone can do anything) for the sake of the exercise. - Code should be tested to a reasonable extent
- Your API must be documented
- You're free to choose any storage mechanism you wish

We expect to be able to run the application locally just by running docker-compose, with no external dependencies required to run it.

## Bonus
- Implement a counter of the shortened URL redirections
- Add an API endpoint to read the shortened URL redirections count
