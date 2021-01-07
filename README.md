# ROK KDM

## Images

https://sagikazarmark.hu/blog/containerizing-a-symfony-application/

### Building DEV
`docker build -t lugal:dev --build-arg APP_ENV=dev .`
`docker build -t lugal-nginx:dev --build-arg ASSET_IMAGE=lugal:dev -f Dockerfile.nginx .`