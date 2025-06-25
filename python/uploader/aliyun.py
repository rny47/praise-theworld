import oss2
from datetime import timedelta, datetime

from .base import OssUploader

class AliyunUploader(OssUploader):
    def __init__(self, access_key: str, secret_key: str, endpoint: str):
        auth = oss2.Auth(access_key, secret_key)
        self.endpoint = endpoint
        self.auth = auth

    def _bucket(self, bucket_name):
        return oss2.Bucket(self.auth, self.endpoint, bucket_name)

    def upload_file(self, bucket: str, file_path: str, object_key: str,
                    expire_seconds: int) -> str:
        bkt = self._bucket(bucket)
        bkt.put_object_from_file(object_key, file_path)
        expire_at = datetime.utcnow() + timedelta(seconds=expire_seconds)
        bkt.put_object_tagging(object_key, {'expire_at': expire_at.isoformat()})
        url = bkt.sign_url('GET', object_key, expire_seconds)
        return url

    def delete_file(self, bucket: str, object_key: str) -> None:
        bkt = self._bucket(bucket)
        bkt.delete_object(object_key)
