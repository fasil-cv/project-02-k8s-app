# project-02-k8s-app
Kubernetes project application deploy E2E




📁 Project Structure
```
application/
├── Dockerfile
├── nginx.conf
└── supervisord.conf
```


Build & Deployment Execution Pipeline
Navigate into your application/ folder and run the execution block below:

Bash
# 1. Build and push the image (Change 'your-registry-username' accordingly)
docker build -t your-registry-username/application:v3 .


# Database information
```
DATABASE_HOST : 'mysql-service'; 
DATABASE_NAME : 'lab-db';
DATABASE_USER : 'job';
DATABASE_PASSWORD 'lab_password_123';
```


# Database init config
```YAML
apiVersion: v1
kind: ConfigMap
metadata:
  name: db-init-script
data:
  init.sql: |
    CREATE DATABASE IF NOT EXISTS `lab-db`;
    USE `lab-db`;
    CREATE TABLE IF NOT EXISTS lab_table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        worker_node VARCHAR(100) NOT NULL,
        status_metric VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
```

