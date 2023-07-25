@servers(['docker' => ['stammsilva@zoomyboy.de']])

@task('deploy', ['on' => 'docker'])
cd $ADREMA_PATH
docker compose down
docker compose pull
docker compose up -d
@endtask
