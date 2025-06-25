import sqlite3
from datetime import datetime
from pathlib import Path

DB_PATH = Path(__file__).parent / 'uploads.db'

INIT_SQL = '''\
CREATE TABLE IF NOT EXISTS uploads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    provider TEXT,
    bucket TEXT,
    object_key TEXT,
    expire_at TEXT
)'''

def init_db():
    """初始化 SQLite 数据库"""
    conn = sqlite3.connect(DB_PATH)
    conn.execute(INIT_SQL)
    conn.commit()
    conn.close()


def add_record(provider: str, bucket: str, object_key: str, expire_at: datetime):
    """新增上传记录"""
    conn = sqlite3.connect(DB_PATH)
    conn.execute(
        'INSERT INTO uploads (provider, bucket, object_key, expire_at) VALUES (?,?,?,?)',
        (provider, bucket, object_key, expire_at.isoformat())
    )
    conn.commit()
    conn.close()


def get_expired(now: datetime):
    """获取所有已过期的记录"""
    conn = sqlite3.connect(DB_PATH)
    cur = conn.execute('SELECT id, provider, bucket, object_key FROM uploads WHERE expire_at <= ?', (now.isoformat(),))
    rows = cur.fetchall()
    conn.close()
    return rows


def delete_record(record_id: int):
    """删除指定记录"""
    conn = sqlite3.connect(DB_PATH)
    conn.execute('DELETE FROM uploads WHERE id=?', (record_id,))
    conn.commit()
    conn.close()
