# project-02-k8s-app
Kubernetes project application deploy E2E




📁 Project Structure
```
application/
├── Dockerfile
├── index.php
├── nginx.conf
├── supervisord.conf
└── deployment.yaml
```


Build & Deployment Execution Pipeline
Navigate into your application/ folder and run the execution block below:

Bash
# 1. Build and push the image (Change 'your-registry-username' accordingly)
docker build -t your-registry-username/application:v2 .
docker push your-registry-username/application:v2
