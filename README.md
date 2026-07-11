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

# Resource name
```
App Deploy  : registry
DB Deploy   : db-registry
App Service : svc-registry
DB Service  : mysql-service
Secret      : db-secret
ConfigMap   : db-config
DB-Host     : db-registry-01.mysql-service
```


# Database init config
```YAML
apiVersion: v1
kind: ConfigMap
metadata:
  name: db-init-script
  namespace: project-02-k8s-app
data:
  init.sql: |
    -- 1. Create the database if it doesn't exist
    CREATE DATABASE IF NOT EXISTS `lab-db`;
    USE `lab-db`;

    -- 2. Create the remote user and explicitly grant permissions
    CREATE USER IF NOT EXISTS 'job'@'%' IDENTIFIED BY 'lab_password_123';
    GRANT ALL PRIVILEGES ON `lab-db`.* TO 'job'@'%';
    FLUSH PRIVILEGES;

    -- 3. Create the profile target table matching the new app layout
    CREATE TABLE IF NOT EXISTS lab_table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        occupation VARCHAR(100) NOT NULL,
        title VARCHAR(100) NOT NULL,
        worker_node VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
```




## Tech Stack
* **Frontend/App:** PHP (PDO), Tailwind CSS, Nginx, Supervisor
* **Database:** MySQL 8.0 with automated ConfigMap SQL initialization
* **Storage:** Decoupled data persistence via PersistentVolumeClaims (PVC)

# StatefulSet Storage & Pod Architecture
<img width="447" height="223" alt="image" src="https://github.com/user-attachments/assets/ebac0ed3-b28e-474d-8782-c20307bc4175" />

