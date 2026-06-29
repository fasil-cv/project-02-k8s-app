from flask import Flask, jsonify
import mysql.connector

app = Flask(__name__)

def db():
    return mysql.connector.connect(
        host="mysql",
        user="appuser",
        password="apppass",
        database="appdb"
    )

@app.route("/users")
def users():
    conn = db()
    cursor = conn.cursor()

    cursor.execute("SELECT id, name FROM users")
    rows = cursor.fetchall()

    result = [{"id": r[0], "name": r[1]} for r in rows]

    cursor.close()
    conn.close()

    return jsonify(result)

@app.route("/health")
def health():
    return {"status": "ok"}

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
