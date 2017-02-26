# Streetart experiment

## Backend

The backend is a Drupal site to store stuff and jsonapi for now to expose its data.

## Frontend

### JSONAPI

The jsonapi frontend is living in app-php. It starts in the main template: ```templates/index.html.twig```.
It fetches all streetart data, and extract the title and image.

