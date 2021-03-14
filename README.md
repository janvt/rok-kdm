# ROK KDM

## Images

https://sagikazarmark.hu/blog/containerizing-a-symfony-application/

### Running DEV
`docker-compose up`

This mounts the current dir into the app / nginx containers.

### Building prod
`docker build -t janvt/lugal:app -f Dockerfile.prod .`
`docker build -t janvt/lugal:web --build-arg ASSET_IMAGE=lugal-app:prod -f Dockerfile.prod.nginx .`

## Queries

### Update Gov Snapshot with Filters

```postgresql
UPDATE governor_snapshot gs
SET kills = NULL
FROM (
    SELECT gs2.id
    FROM governor_snapshot gs2, governor g, alliance a
    WHERE gs2.governor_id = g.id
    AND g.alliance_id = a.id
    AND a.tag = 'DG70'
    AND gs2.kills = gs2.t4_kills
) ids
WHERE gs.id IN(ids.id)
```