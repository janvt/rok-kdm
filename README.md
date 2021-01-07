# ROK KDM

## Images

https://sagikazarmark.hu/blog/containerizing-a-symfony-application/

### Running DEV
`docker-compose up`

This mounts the current dir into the app / nginx containers.

### Building prod
`docker build -t lugal-app:prod -f Dockerfile.prod .`
`docker build -t lugal-web:prod --build-arg ASSET_IMAGE=lugal-app:prod -f Dockerfile.prod.nginx .`