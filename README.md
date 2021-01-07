# ROK KDM

## Images

https://sagikazarmark.hu/blog/containerizing-a-symfony-application/

### Running DEV
`docker-compose up`

This mounts the current dir into the app / nginx containers.

### Building prod
`docker build -t janvt/lugal:app -f Dockerfile.prod .`
`docker build -t janvt/lugal:web --build-arg ASSET_IMAGE=lugal-app:prod -f Dockerfile.prod.nginx .`