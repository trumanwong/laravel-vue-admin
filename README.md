# Laravel Vue Admin

[Laravel Vue Admin](https://laravel-vue-admin.eu.org) is a beautiful dashboard combination of [Laravel](https://laravel.com/), [Vue3](https://github.com/vuejs/vue) and the UI Toolkit [Element Plus](https://element-plus.org/).

## Getting started

### Installing

#### Manual

```bash
# Clone the project and run composer
git clone https://github.com/trumanwong/laravel-vue-admin
cd laravel-vue-admin

# Migration and DB seeder (after changing your DB settings in .env)
php artisan migrate --seed

# Install dependency with NPM
npm install

# develop
npm run watch

# Build on production
npm run build
```

#### Docker

```sh
docker-compose up -d
```

Build static files within `Laravel` container with `npm`

```sh
docker exec -it laravel-vue-admin npm run watch
```

Open http://localhost:8000 (laravel container port declared in `docker-compose.yml`) to access Laravel Vue Admin.

## Built with

* [Laravel](https://laravel.com/) - The PHP Framework For Web Artisans
* [Laravel Sanctum](https://github.com/laravel/sanctum/) - Laravel Sanctum provides a featherweight authentication system for SPAs and simple APIs.
* [spatie/laravel-permission](https://github.com/spatie/laravel-permission) - Associate users with permissions and roles.
* [VueJS](https://vuejs.org/) - The Progressive JavaScript Framework
* [Element Plus](https://element-plus.org/) -A Vue.js 3 UI library
* [vue3-admin-ts](https://github.com/jzfai/vue3-admin-ts) - A minimal vue3 admin template with Element-Plus UI & axios & permission control & lint & hook

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE) file for details.

## Acknowledgements

* [Laravel](https://laravel.com/) - The PHP Framework For Web Artisans
* [VueJS](https://vuejs.org/) - The Progressive JavaScript Framework
* [vue3-admin-ts](https://panjiachen.github.io/vue-element-admin/#/) A minimal vue3 admin template with Element-Plus UI & axios & permission control & lint & hook
* [Echarts](http://echarts.apache.org/) - A powerful, interactive charting and visualization library for browser.
* [Cloudflare](https://https://www.cloudflare.com/) - A global network built for the cloud
