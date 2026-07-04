# build image

### image name
Note: use the image name `devapp` for this DevOps project
`devapp` - `development and DevOps projects app`


### docker command
```
sudo docker build --no-cache -t fasilcv/devapp:v1.0.1 .
```

### podman command
```
podman build --no-cache -t fasilcv/devapp:v1.0.1 .
```

```
docker tag fasilcv/devapp:v1.0.1 devapp:latest
```


```
podman push fasilcv/devapp:v1.0.1
```

```
podman push fasilcv/devapp:latest
```
