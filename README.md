# project-02-k8s-app
Kubernetes project application deploy E2E


- Nginx = frontend
- Python Flask = backend (very lightweight)
- MySQL = optional check



📁 Project Structure
```
project/
├── docker/
│   ├── frontend/
│   │   ├── Dockerfile
│   │   └── index.html
│   │
│   ├── backend/
│   │   ├── Dockerfile
│   │   ├── app.py
│   │   └── requirements.txt
│
└── k8s/
    ├── mysql.yaml
    ├── backend.yaml
    ├── frontend.yaml
    ├── services.yaml
```


# MySQL data create
⚠️ Important

The initialization scripts run only once—when the MySQL data directory is empty.

If you're using a PersistentVolume (PVC) and restart the pod, the script will not run again because the database already exists.

## Example ConfigMap
```YAML
apiVersion: v1
kind: ConfigMap
metadata:
  name: mysql-initdb
data:
  init.sql: |
    CREATE TABLE users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100),
      email VARCHAR(100)
    );

    INSERT INTO users (name, email) VALUES
    ('Fasil', 'fasil@fcvlab.com'),
    ('Alice', 'alice@example.com'),
    ('Bob', 'bob@example.com');
```
