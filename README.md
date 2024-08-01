# Ejsabet

Inventory Management System for Textile Factory

## Features

- Nuxt 2
- Laravel 8
- SPA or SSR
- Socialite integration
- VueI18n + ESlint + Bootstrap 4 + Font Awesome 5

## Installation

- `composer create-project --prefer-dist phyzerbert/alzex-v2`
- Edit `.env` and set your database connection details
- (When installed via git clone or download, run `php artisan key:generate` and `php artisan jwt:secret`)
- `php artisan migrate`
- `npm install`

## Usage

### Development

```bash
# start Laravel
php artisan serve

# start Nuxt
npm run dev
```

Access your application at `http://localhost:3000`.

### Production

```bash
npm run build
```

### Enable SSR

- Edit `client/nuxt.config.js` and set `ssr: true`
- Edit `.env` to set `APP_URL=http://api.example.com` and `CLIENT_URL=http://example.com`
- Run `npm run build` and `npm run start`

#### Nginx Proxy

For Nginx you can add a proxy using the follwing location block:

```
server {
    location / {
        proxy_pass http://http://127.0.0.1:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }
}
```

#### Process Manager

In production you need a process manager to keep the Node server alive forever:

```bash
# install pm2 process manager
npm install -g pm2

# startup script
pm2 startup

# start process
pm2 start npm --name "laravel-nuxt" -- run start

# save process list
pm2 save

# list all processes
pm2 l
```

After each deploy you'll need to restart the process:

```bash
pm2 restart laravel-nuxt
```

Make sure to read the [Nuxt docs](https://nuxtjs.org/).

```bash
php artisan encryptable:encryptModel 'App\Models\Transaction'
```

#### Added Fields
change roles user=>admin, admin=>super_admin
transactions.authorized_by_id
transactions.is_approved_by_super_admin
change type of categories.status from int to string. default is active. available values are active, inactive, disabled.
add field categories.must_be_approved_by_super_admin boolean
add field transactions.is_uploading boolean default false
add field categories.must_be_approved_from timestamp nullable

Thanks for considering my project