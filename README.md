# Bazaar Scraper Stack
This is a proof of concept. The stack is setup mainly for dev purposes and optimized for convenience during development over efficiency: for example all the volumes with code are only mounted not copied - this allowes changes to be made without the need to rebuilt the containers.

## Setup
```
cd docker
docker-compose up
```
⚠ During the first run both of the scraper containers will exit with code 1. Migrations need to be executed form the host first.


## Migrations
After the mysql container starts up run the migration form the scraper folder:
```
cd scraper
./vendor/bin/phinx migrate
```

Now we can add the rootPages we want to scrape into the database (`root_pages`). The `resource` names are `s-bazar` and `cyklo-bazar`. And run the scrapers again.


## Scrapers
```
docker-compose start sbazar cyklobazar
```

## API
The api is available at `http://localhost:8080/api/v1` currently the only endpoint is `/items`.
