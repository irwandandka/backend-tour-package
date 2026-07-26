// Scripted pipeline (not declarative) - needed to run a Postgres sidecar
// container alongside the PHP one. Testing against Postgres here (not the
// repo's default SQLite) is deliberate: the migration fixes made for the
// real homelab deployment only added Postgres-safe branches on top of the
// original MySQL-style code, which is NOT SQLite-compatible - CI should
// exercise the same database engine that's actually deployed.
node {
    checkout scm

    // No --network-alias here: the default bridge network Jenkins' docker
    // run uses doesn't support network-scoped aliases. --link below
    // already gives the linked container the ci-postgres hostname via
    // legacy Docker linking, which works fine on the default bridge.
    def pg = docker.image('postgres:15-alpine').run(
        '-e POSTGRES_USER=laravel_dev -e POSTGRES_PASSWORD=laravel_dev -e POSTGRES_DB=laravel_dev'
    )

    try {
        docker.image('php:8.2-cli').inside("--link ${pg.id}:ci-postgres") {
            stage('System deps') {
                sh '''
                    apt-get update && apt-get install -y \
                        git unzip libzip-dev libpng-dev libonig-dev libxml2-dev libicu-dev libpq-dev
                    docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip intl sockets soap
                    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
                '''
            }
            stage('Install') {
                // --no-scripts: composer.json's post-autoload-dump calls
                // `artisan filament:upgrade`, a leftover reference to a
                // package that isn't in require/require-dev - it crashes
                // composer install otherwise. package:discover (the hook
                // that's actually needed) is run manually below instead.
                sh 'composer install --no-dev --optimize-autoloader --no-interaction --no-scripts'
                sh 'php artisan package:discover --ansi'
            }
            stage('Lint (Pint)') {
                sh './vendor/bin/pint --test'
            }
            stage('Static analysis (Larastan)') {
                sh './vendor/bin/phpstan analyse --no-progress'
            }
            stage('Test') {
                withEnv([
                    'DB_CONNECTION=pgsql',
                    'DB_HOST=ci-postgres',
                    'DB_PORT=5432',
                    'DB_DATABASE=laravel_dev',
                    'DB_USERNAME=laravel_dev',
                    'DB_PASSWORD=laravel_dev',
                ]) {
                    sh 'cp .env.example .env'
                    sh 'php artisan key:generate --ansi'
                    sh 'php artisan migrate --graceful --ansi'
                    sh './vendor/bin/phpunit'
                }
            }
        }
    } finally {
        pg.stop()
    }
}
